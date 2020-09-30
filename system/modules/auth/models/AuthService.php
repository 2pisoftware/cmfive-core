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
        $user = $this->db->get("user")->where("login", $login)->and("is_deleted", 0)->fetch_row();
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
    public function getTitles() : array
    {
        return LookupService::getInstance($this->w)->getLookupByType("title");
    }

    public function getContact($contact_id)
    {
        return $this->getObject("Contact", ['id' => $contact_id]);
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
            $this->Log->error("Denied access: module '" . urlencode($parts['module']) . "' doesn't exist");
            self::$_cache[$key] = false;
            return false;
        }

        if ((function_exists("anonymous_allowed") && anonymous_allowed($this->w, $path)) || ($this->user() && $this->user()->allowed($path))) {
            self::$_cache[$key] = $url ? $url : true;
            return self::$_cache[$key];
        }

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
                    }
                } else {
                    $this->Log->info($module . ' did not provide passthrough user for:' . $username);
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
    public function getAllRoles()
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
        $roleUsers = array();
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
        $rows = $this->_db->get("user")->where(['is_active' => 1, 'is_deleted' => 0, 'is_group' => 1])->fetch_all();

        if ($rows) {
            $objects = $this->fillObjects("User", $rows);

            return $objects;
        }
        return null;
    }

    public function getGroupMembers($group_id = null, $user_id = null)
    {
        if ($group_id) {
            $option['group_id'] = $group_id;
        }

        if ($user_id) {
            $option['user_id'] = $user_id;
        }

        $groupMembers = $this->getObjects("GroupUser", $option, true);

        if ($groupMembers) {
            return $groupMembers;
        }
        return null;
    }

    public function getGroupMemberById($id)
    {
        $groupMember = $this->getObject("GroupUser", $id);

        if ($groupMember) {
            return $groupMember;
        }
        return null;
    }

    public function getRoleForLoginUser($group_id, $user_id)
    {
        $groupMember = $this->getObject("GroupUser", ['group_id' => $group_id, 'user_id' => $user_id]);

        if ($groupMember) {
            return $groupMember->role;
        }
        return null;
    }
}
