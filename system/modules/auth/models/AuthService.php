<?php

class AuthService extends DbService
{
    public $_roles;
    public $_roles_loaded = false;
    public $_rest_user = null;
    private static $_cache = [];

    public function login($login, $password, $client_timezone, $skip_session = false, $mfa_code = "")
    {
        $credentials['login'] = $login;
        $credentials['password'] = $password;
        $hook_results = $this->w->callHook("auth", "prelogin", $credentials);

        if (!empty($hook_results)) {
            foreach ($hook_results as $module => $user) {
                if (!empty($user)) {
                    LogService::getInstance($this->w)->info($user->getFullName() . " authenticated via " . $module . " prelogin hook");
                    break;
                } else {
                    LogService::getInstance($this->w)->info('prelogin hook did not provide authentication: ' . $login);
                }
            }
        }

        /** @var User $user */
        if (empty($user)) {
            $user = $this->getUserForLogin($login);
            if (empty($user)) {
                LogService::getInstance($this->w)->info('cmfive user does not exist: ' . $login);
                return null;
            }

            // If the User's password salt is empty use password_verify because the salt is built into the password, otherwise use the depreicated way.
            if (empty($user->password_salt)) {
                if (!password_verify($password, $user->password)) {
                    LogService::getInstance($this->w)->info('cmfive pasword mismatch for username: ' . $login);
                    return null;
                }
            } else {
                if ($user->encryptPassword($password) !== $user->password) {
                    LogService::getInstance($this->w)->info('cmfive pasword mismatch for username: ' . $login);
                    return null;
                }
            }

            if ($user->is_external == 1) {
                LogService::getInstance($this->w)->info('cmfive user is external: ' . $login);
                return null;
            }

            if ($user->is_mfa_enabled && !$user->checkMfaCode($mfa_code)) {
                LogService::getInstance($this->w)->setLogger("AUTH")->warning("User attempted to login with invalid MFA code");
                return null;
            }
        }

        // Check if the User's password hash is depricated and update if so.
        if ($user->updatePasswordHash($password)) {
            LogService::getInstance($this->w)->info("User with ID: " . $user->id . " password hash was updated");
        }

        LogService::getInstance($this->w)->info("User logged in: " . $user->getFullName());
        //allow post login hook to do whatever
        $hook_results = $this->w->callHook("auth", "postlogin", $user);
        $user->updateLastLogin();

        if (!$skip_session) {
            $this->w->session('user_id', $user->id);
            $this->w->session('timezone', $client_timezone);
        }

        return $user;
    }
    public function externalLogin($login, $password, $skip_session = false)
    {
        $user = $this->getUserForLogin($login);
        if (empty($user->id) || ($user->encryptPassword($password) !== $user->password) || $user->is_external == 0) {
            return null;
        }


        $user->updateLastLogin();
        if (!$skip_session) {
            $this->w->session('user_id', $user->id);
        }
        return $user;
    }

    public function forceLogin($user_id = null)
    {
        if (empty($user_id)) {
            return;
        }

        $user = $this->getUser($user_id);
        if (empty($user->id)) {
            return null;
        }

        $user->updateLastLogin();
        $this->w->session('user_id', $user->id);
    }

    public function recordLoginAttempt(string $login)
    {
        if (Config::get('auth.login.attempts.track_attempts', false) !== true) {
            return;
        }

        $max_attempts = Config::get('auth.login.attempts.max_attempts', 5);

        $user = $this->getUserForLogin($login);

        if (!empty($user->id)) {
            if (empty($user->login_attempts)) {
                if ($max_attempts == 1) {
                    // Lock the account after one failed attempt
                    $user->lock();
                }
                $user->login_attempts = 1;
                $user->update();
            } else {
                if ($max_attempts <= ++$user->login_attempts) {
                    $user->lock();
                } else {
                    $user->update();
                }
            }
        }
    }

    public function _web_init()
    {
        $this->_loadRoles();
    }

    /**
     * Returns a user ID if they are logged in
     *
     * @return mixed|null a user ID
     */
    public function loggedIn()
    {
        return $this->w->session('user_id');
    }

    /**
     * Returns a User from the passed login parameter.
     *
     * @param string $login
     * @return User
     */
    public function getUserForLogin($login)
    {
        $user = $this->_db->get("user")->where("login", $login)->and("is_deleted", 0)->fetchRow();
        return $this->getObjectFromRow("User", $user);
    }

    public function getUserForToken($token)
    {
        return $this->getObject("User", ["password_reset_token" => $token]);
    }

    public function setRestUser($user)
    {
        $this->_rest_user = $user;
    }

    /**
     * There is no way to enforce the creation of a User object when creating a Contact
     * E.g. for an address book, there is no need to create a User object. However, if you
     * want to ensure that a Contact will have a User account, call this public function before doing
     * anything else with the Contact.
     *
     * As a security measure, all user accounts created this way are external only.
     *
     * @param mixed $contact_id
     * @return int user_id
     */
    public function createExernalUserForContact($contact_id)
    {
        $contact = $this->getContact($contact_id);

        if (empty($contact->id)) {
            return false;
        }

        $user = $contact->getUser();
        if (!empty($user->id)) {
            return $user->id;
        }

        // Check that we dont already have an external user account, but not linked to the contact
        $user = $this->getUserForLogin($contact->email);
        // If there is an existing user account
        if (!empty($user->id)) {
            if ($user->is_external == 1) {
                $existing_user_contact = $user->getContact();

                // Merge both contact objects together
                $merge_object_property = function (&$source, &$destination, $property) {
                    if (property_exists($source, $property) && property_exists($destination, $property)) {
                        if (empty($destination->$property)) {
                            $destination->$property = $source->$property;
                        }
                    }
                };
                $merge_object_property($existing_user_contact, $contact, "firstname");
                $merge_object_property($existing_user_contact, $contact, "lastname");
                $merge_object_property($existing_user_contact, $contact, "othername");
                $merge_object_property($existing_user_contact, $contact, "title_lookup_id");
                $merge_object_property($existing_user_contact, $contact, "homephone");
                $merge_object_property($existing_user_contact, $contact, "workphone");
                $merge_object_property($existing_user_contact, $contact, "mobile");
                $merge_object_property($existing_user_contact, $contact, "priv_mobile");
                $merge_object_property($existing_user_contact, $contact, "fax");
                $merge_object_property($existing_user_contact, $contact, "email");

                // Update contact reference for user
                if ($contact->update()) {
                    $user->contact_id = $contact->id;
                    $user->update();

                    // Delete one of them
                    $existing_user_contact->delete();

                    return $user->id;
                }

                LogService::getInstance($this->w)->setLogger("AUTH")->error("Could not merge duplicate external contacts");
                return false;
            } else {
                return false;
            }
        }


        $user = new User($this->w);
        $user->login = $contact->email;
        $user->is_external = 1;
        $user->contact_id = $contact->id;
        $user->insert();

        return $user->id;
    }

    public function getContacts()
    {
        return $this->getObjects('Contact', ['is_deleted' => 0]);
    }

    /**
     * Returns an array of titles from the lookup table.
     *
     * @return array[Lookup]
     */
    public function getTitles(): array
    {
        return LookupService::getInstance($this->w)->getLookupByType("title");
    }

    /**
     * Returns a contact via its id.
     *
     * @param string $id
     * @return Contact|null
     */
    public function getContact($id)
    {
        return $this->getObject("Contact", ['id' => $id]);
    }

    public function getContactByEmail($email)
    {
        return $this->getObject("Contact", ['email' => filter_var($email, FILTER_SANITIZE_EMAIL), 'is_deleted' => 0]);
    }

    /**
     * Return the logged in user based on the session variable user_id.
     *
     * If a user has been set from a REST service, then that user will
     * be returned.
     *
     * @return User|NULL
     */
    public function user()
    {
        // special case where RestService handles authentication
        if ($this->_rest_user) {
            return $this->_rest_user;
        }

        // normal session based authentication
        if ($this->loggedIn()) {
            return $this->getObject("User", $this->w->session('user_id'));
        }
        return null;
    }

    /**
     *
     * checks if the CURRENT user has this role
     */
    public function hasRole($role)
    {
        return $this->user() ? $this->user()->hasRole($role) : false;
    }

    /**
     *
     * Check if the current user can access the specified path
     * @ return false if the login user is not allowed access to this path
     *  OR return string url if it is provided as a parameter
     */
    public function allowed($path, $url = null)
    {
        $key = $path . '::' . $url;
        if (!empty(self::$_cache[$key])) {
            return self::$_cache[$key];
        }
        $parts = $this->w->parseUrl($path);
        if (!in_array($parts['module'], $this->w->modules())) {
            LogService::getInstance($this->w)->error("Denied access: module '" . urlencode($parts['module']) . "' doesn't exist");
            self::$_cache[$key] = false;
            return false;
        }

        // Whitelisted action, or white-bread login session
        if ((function_exists("anonymous_allowed") && anonymous_allowed($this->w, $path)) || ($this->user() && $this->user()->allowed($path))) {
            self::$_cache[$key] = $url ? $url : true;
            return self::$_cache[$key];
        }

        // API token handling:
        // If I have an authentication header: and it has a token -> else fallthrough to original logic
        // ie: expecting [...curl...etc...] -H "Authorization: Bearer {token}"
        /*
                Note! If under Apache & HTTP_AUTHORIZATION is dropped, prove site HTPPS and then patch access:
                RewriteEngine On
                RewriteCond %{HTTP:Authorization} ^(.+)$
                RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
                */

        if (empty($this->user()) && (Config::get('system.use_api') === true) && !empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $speculativeToken = TokensService::getInstance($this->w)->getTokenFromAuthorisationHeader($_SERVER['HTTP_AUTHORIZATION']);
            if (!empty($speculativeToken)) {
                // call for a module to assert the token is valid
                $hook_results = $this->w->callHook("auth", "get_auth_token_validation", $speculativeToken);
            }

            // if the token is invalid( jwt fails checks, len == 0 or somesuch) then we stop and don't continue
            if (empty($speculativeToken) || empty($hook_results)) {
                LogService::getInstance($this->w)->error("Key invalid: '" . ($_SERVER['HTTP_AUTHORIZATION'] ?? "!NONE!") . "' was provided");
                ApiOutputService::getInstance($this->w)->apiRefuseMessage($path,"Token not valid");
                self::$_cache[$key] = false;
                return false;
            }
            foreach ($hook_results as $module => $validatingToken) {
                if (is_a($validatingToken, "TokensPolicy") && $validatingToken->tokensAllowed($path)) {
                    self::$_cache[$key] = $url ? $url : true;
                    return self::$_cache[$key];
                } else {
                    LogService::getInstance($this->w)->info('Handler ' . $module . ' did not provide Auth');
                }
            }
            ApiOutputService::getInstance($this->w)->apiRefuseMessage($path.":[".$speculativeToken."]", "Token not authenticated");
            self::$_cache[$key] = false;
            return false;
        }


        // Allow forced user-login if any module will vouch for web server asserted identity
        if (empty($this->user()) && (Config::get('system.use_passthrough_authentication') === true) && !empty($_SERVER['AUTH_USER'])) {
            // Get the username
            $username = explode('\\', $_SERVER["AUTH_USER"]);
            $username = end($username);
            LogService::getInstance($this->w)->debug("Passthrough Username: " . $username);

            //this hook returns $hook_results[$module][0]=$user or null.
            $hook_results = $this->w->callHook("auth", "get_user_for_passthrough", $username);
            foreach ($hook_results as $module => $user) {
                if (!empty($user) && $user instanceof User) {
                    $this->forceLogin($user->id);
                    if ($user->allowed($path)) {
                        self::$_cache[$key] = $url ? $url : true;
                        // Observed during work for token handler:
                        // Here, we have forced login, 
                        // But do we mean for it to still bounce 1x through auth/login as redirect?
                        // In standing core releases, a _cache[key] 'return' is omitted here
                        // = noting it was required by new tokens model!
                        // Possibly this block should also have return thus:
                        // return self::$_cache[$key]; 
                    }
                } else {
                    LogService::getInstance($this->w)->info($module . ' did not provide passthrough user for:' . $username);
                }
            }
        }

        self::$_cache[$key] = false;
        return false;
    }

    /**
     * Return an array of role names for all available roles
     *
     * @return array of strings
     */
    public function getAllRoles(): array
    {
        $this->_loadRoles();
        if (!$this->_roles) {
            $roles = [];

            $funcs = get_defined_functions();
            foreach ($funcs['user'] as $f) {
                if (preg_match("/^role_(.+)_allowed$/", $f, $matches)) {
                    $roles[] = $matches[1];
                }
            }
            $this->_roles = $roles;
        }
        return $this->_roles;
    }

    public function _loadRoles()
    {
        // do this only once
        if ($this->_roles_loaded) {
            return;
        }

        $modules = $this->w->modules();
        foreach ($modules as $model) {
            $file = $this->w->getModuleDir($model) . $model . ".roles.php";
            if (file_exists($file)) {
                require_once $file;
            }
        }
        $this->_roles_loaded = true;
    }

    /**
     * Returns a user via its id.
     *
     * @param string $id
     * @return User|null
     */
    public function getUser($id)
    {
        return $this->getObject("User", $id);
    }

    public function getUsersAndGroups($includeDeleted = false)
    {
        $where = [
            "is_active" => 1,
            "is_external" => 0
        ];

        if (!$includeDeleted) {
            $where["is_deleted"] = 0;
        }
        return $this->getObjects("User", $where);
    }

    public function getUsers($includeDeleted = false)
    {
        $where = [
            "is_group" => 0,
            "is_active" => 1,
            "is_external" => 0
        ];

        if (!$includeDeleted) {
            $where["is_deleted"] = 0;
        }
        return $this->getObjects("User", $where);
    }

    public function getUserForContact($cid)
    {
        return $this->getObject("User", ["contact_id" => $cid]);
    }

    public function getUsersForRole($role)
    {
        if (!$role) {
            return null;
        }
        $users = $this->getUsersAndGroups();
        $roleUsers = [];
        if ($users) {
            foreach ($users as $u) {
                if ($u->hasRole($role)) {
                    $roleUsers[] = $u;
                }
            }
        }
        return $roleUsers;
    }

    public function getGroups()
    {
        return $this->getObjects("User", ['is_active' => 1, 'is_deleted' => 0, 'is_group' => 1]);
    }

    public function getGroupMembers($group_id = null, $user_id = null)
    {
        if ($group_id) {
            $option['group_id'] = $group_id;
        }

        if ($user_id) {
            $option['user_id'] = $user_id;
        }

        return $this->getObjects("GroupUser", $option, true);
    }

    public function getGroupMemberById($id)
    {
        return $this->getObject("GroupUser", $id);
    }

    public function getRoleForLoginUser($group_id, $user_id)
    {
        $groupMember = $this->getObject("GroupUser", ['group_id' => $group_id, 'user_id' => $user_id]);

        if ($groupMember) {
            return $groupMember->role;
        }
        return null;
    }

    public function getSettingByKey(string $key)
    {
        if ($this->loggedIn()) {
            return $this->getObject('UserSetting', ['user_id' => $this->user()->id, 'setting_key' => $key]);
        }
    }

    /**
     * Function to recursively check if a user is a member of a group (or parent group)
     * 
     * @param int|string $group_id
     * @param int|string $user_id
     * @return bool
     */
    public function isUserGroupMemberRecursive(int|string $group_id, int|string $user_id) : bool {
        $groupMembers = $this->getGroupMembers($group_id);
        if (!empty($groupMembers)) {
            foreach ($groupMembers as $groupMember) {
                if ($groupMember->user_id === $user_id) {
                    return true;
                } elseif ($this->getUser($groupMember->user_id)->is_group) {
                    if ($this->isUserGroupMemberRecursive($groupMember->user_id, $user_id)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
