<?php

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

/**
 * User object
 *
 * @author Carsten Eckelmann, May 2014
 *
 */
class User extends DbObject
{
    public $login;
    public $is_admin;
    public $password;
    public $password_salt;
    public $is_active;
    public $dt_lastlogin;
    public $dt_created;
    public $contact_id;
    public $is_deleted;
    public $is_group;
    public $password_reset_token;
    public $dt_password_reset_at;
    public $redirect_url;
    public $is_external;
    public $_roles;
    public $_contact;
    public $_modifiable;
    public $language;
    public $is_password_invalid;
    public $is_mfa_enabled;
    public $mfa_secret;
    public $login_attempts;
    public $is_locked;

    public function lock()
    {
        $this->is_locked = 1;
        $this->update();
    }

    public function unlock()
    {
        $this->is_locked = 0;
        $this->login_attempts = 0;
        $this->update();
    }

    public function resetAttempts()
    {
        $this->login_attempts = 0;
        $this->update();
    }

    /**
     * Check if the passed MFA code is correct.
     *
     * @param string $mfa_code
     *
     * @return bool
     */
    public function checkMfaCode(string $mfa_code) : bool
    {
        return (new GoogleAuthenticator())->checkCode($this->mfa_secret, $mfa_code);
    }

    /**
     * A static array of string arrays to be used for validation when creating forms with a User in it.
     *
     * @var array[array[string]]
     */
    public static $_validation = [
        'login' => ['required']
    ];

    public function getAvailableLanguages()
    {
        return [
            [
                __("English"), "en_US.UTF-8"
            ],
            [
                __("German"), "de_DE.UTF-8"
            ],
            [
                __("French"), "fr_FR.UTF-8"
            ],
            [
                __("Chinese"), "zh_CN.UTF-8"
            ],
            [
                __("Japanese"), "ja_JP.UTF-8"
            ],
            [
                __("Spanish"), "es_ES.UTF-8"
            ],
            [
                __("Dutch"), "nl_NL.UTF-8"
            ],
            [
                __("Russian"), "ru_RU.UTF-8"
            ],
            [
                __("Gaelic"), "gd_GB.UTF-8"
            ]
        ];
    }

    public function delete($force = false)
    {
        try {
            $this->startTransaction();

            $contact = $this->getContact();
            if ($contact) {
                $contact->delete($force);
            }

            parent::delete($force);
            $this->commitTransaction();
        } catch (Exception $e) {
            // The error should already be logged
            $this->rollbackTransaction();
            return false;
        }

        return true;
    }

    /**
     * Returns a user's contact by its $contact_id
     *
     * @return Contact|null
     */
    public function getContact()
    {
        if (!$this->_contact) {
            $this->_contact = $this->getObject("Contact", $this->contact_id);
        }
        return $this->_contact;
    }

    /**
     * @param integer $group_id
     * @return array<GroupUser> if this user is in the group with the id
     */
    public function isInGroups($group_id = null)
    {
        return isset($group_id) ? $this->getObjects("GroupUser", [
            'user_id' => $this->id,
            'group_id' => $group_id,
        ]) : $this->getObjects("GroupUser", ['user_id' => $this->id]);
    }

    /**
     * Check if this user is member of the group.
     *
     * (Reminder: Groups are special User objects! so don't get confused
     *  that the $group is a User)
     *
     * @param User $group
     * @return true if this user is part of this group
     */
    public function inGroup(User $group)
    {
        $groupmembers = AuthService::getInstance($this->w)->getGroupMembers($group->id, null);

        if ($groupmembers) {
            foreach ($groupmembers as $member) {
                if ($member->user_id == $this->id) {
                    return true;
                }

                $usr = AuthService::getInstance($this->w)->getUser($member->user_id);
                if (!empty($usr) && $usr->is_group == 1 && $this->inGroup($usr)) {
                    return true;
                }
            }
        }
    }

    public function getFirstName()
    {
        $contact = $this->getContact();

        if (!empty($contact->id)) {
            return $contact->getFirstName();
        }
        return '';
    }

    public function getSurname()
    {
        $contact = $this->getContact();
        if (!empty($contact->id)) {
            return $contact->getSurname();
        }
        return '';
    }

    public function getFullName()
    {
        $contact = $this->getContact();
        $name = ucfirst($this->login);
        if ($contact) {
            $name = $contact->getFullName();
        }
        return $name;
    }

    public function getSelectOptionTitle()
    {
        return $this->getFullName();
    }

    public function getSelectOptionValue()
    {
        return $this->id;
    }

    /**
     * @return string, either the login or first name
     */
    public function getShortName()
    {
        $contact = $this->getContact();
        $name = ucfirst($this->login);
        if ($contact) {
            $name = $contact->firstname;
        }
        return $name;
    }

    /**
     * @param string $force
     * @return array array of all roles that this user has
     */
    public function getRoles($force = false)
    {
        if ($this->is_admin) {
            return AuthService::getInstance($this->w)->getAllRoles();
        }
        if (!$this->_roles || $force) {
            $this->_roles = [];

            $groupUsers = $this->isInGroups();

            if ($groupUsers) {
                foreach ($groupUsers as $groupUser) {
                    $groupRoles = $groupUser->getGroupRoles();

                    foreach ($groupRoles as $groupRole) {
                        if (!in_array($groupRole, $this->_roles)) {
                            $this->_roles[] = $groupRole;
                        }
                    }
                }
            }
            $rows = $this->getObjects("UserRole", ["user_id" => $this->id]);

            if ($rows) {
                foreach ($rows as $row) {
                    if (!in_array($row->role, $this->_roles)) {
                        $this->_roles[] = $row->role;
                    }
                }
            }
        }
        return $this->_roles;
    }

    /**
     * update the last login field in the database
     */
    public function updateLastLogin()
    {
        $this->dt_lastlogin = $this->time2Dt(time());
        $this->update();
    }

    /**
     * Check whether a user has this role
     *
     * @param string $role
     * @return true if and only if the user has this role
     */
    public function hasRole($role, $strict = false)
    {
        if ($this->is_admin && !$strict) {
            return true;
        }
        if ($this->getRoles(true)) {
            return is_array($this->_roles) && in_array($role, $this->_roles);
        } else {
            return false;
        }
    }

    /**
     * Check whether a user has a role in a list of roles
     *
     * @param array $roles
     * @return true if the user has any one of these roles
     */
    public function hasAnyRole($roles, $strict = false)
    {
        if ($this->is_admin && !$strict) {
            return true;
        }
        if (!empty($roles)) {
            if (is_array($roles)) {
                foreach ($roles as $r) {
                    if ($this->hasRole($r)) {
                        return true;
                    }
                }
            } elseif (is_string($roles)) {
                if ($this->hasRole($roles)) {
                    return true;
                }
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Add a role to this user
     *
     * @param string a role
     */
    public function addRole($role)
    {
        if (!$this->hasRole($role)) {
            $ur = new UserRole($this->w);
            $ur->user_id = $this->id;
            $ur->role = $role;
            $ur->insert();
        }
    }

    /**
     * Remove a role from this user
     *
     * @param string $role
     */
    public function removeRole($role)
    {
        if ($this->hasRole($role)) {
            $role = AdminService::getInstance($this->w)->getObject("UserRole", ["user_id" => $this->id, "role" => $role]);
            if (!empty($role)) {
                $role->delete();
            }
            $this->getRoles(true);
        }
    }

    /**
     * Check whether a user is allowed to navigate to a certain url
     * in the system.
     *
     * This will execute all the functions associated to the user's roles
     * until one function returns true.
     *
     * @param Web $w
     * @param string $path
     * @return true if one role function returned true
     */
    public function allowed($path)
    {
        if ($this->is_locked) {
            unset($_SESSION['user_id']);
            $this->w->error("This account is locked, most likely due to too many login attempts. Please contact an Administrator to get your account unlocked", "/auth/login");
        }
        
        if (!$this->is_active) {
            return false;
        }
        if ($this->is_admin) {
            return true;
        }

        $roles = $this->getRoles() ?? [];

        foreach ($roles as $rn) {
            $rolefunc = "role_" . $rn . "_allowed";
            if (function_exists($rolefunc)) {
                if ($rolefunc($this->w, $path)) {
                    return true;
                }
            } else {
                LogService::getInstance($this->w)->error("Role '" . $rn . "' does not exist!");
            }
        }

        return false;
    }

    /**
     * Encrypt the password using sha1 or password_hash depending on whether the User's salt is build into the
     * password hash and the PHP Version.
     *
     * @param string $password
     * @return string
     */
    public function encryptPassword($password)
    {
        // If User's password salt is not built into the password hash use SHA1.
        // Should not be used in Cmfive v5
        // if (!empty($this->password_salt)) {
        //     return sha1($this->password_salt . $password);
        // }

        $hash = false;
        $algorithm = PASSWORD_DEFAULT;
        $options = [];

        // If the password hash is using BYCRYPT set the algorithm accordingly.
        if (startsWith($this->password, "$2y$")) {
            $algorithm = PASSWORD_BCRYPT;
        }

        // If the password hash is not using BYCRYPT and the PHP version is at least 7.3.0 set the
        // password hash is using ARGON2. Set the options accordingly.
        if (!startsWith($this->password, "$2y$") && version_compare(PHP_VERSION, "7.3.0", ">=")) {
            $options = [
                "memory_cost" => PASSWORD_ARGON2_DEFAULT_MEMORY_COST, // Max 1024 bytes.
                "time_cost" => PASSWORD_ARGON2_DEFAULT_TIME_COST, // Max 2 seconds.
                "threads" => PASSWORD_ARGON2_DEFAULT_THREADS]; // Max 2 threads.
        }

        $hash = password_hash($password, $algorithm, $options);

        return $hash === false ? "" : $hash;
    }

    /**
     * Set the user's password and encrypt it using sha1 or password_hash depending on whether the user has a salt or
     * not.
     *
     * @param string $password
     * @param boolean $update_salt - Deprecated, will be removed in v5.0.0.
     */
    public function setPassword($password, $update_salt = true)
    {
        $this->password = $this->encryptPassword($password);
        $this->w->callHook('auth', 'setpassword', [$password, $this]);
    }

    /**
     * If the User's password hash is deprecated and the $password parameter matches the User's password,
     * update the User's password to use the latest Hash.
     *
     * @param string $password
     * @return boolean
     */
    public function updatePasswordHash($password)
    {
        if ($this->password !== $this->encryptPassword($password)) {
            return false;
        }

        if (!empty($this->password_salt)) {
            $this->password_salt = null;
        }

        // $this->setPassword($password);
        return $this->update(true);
    }

    // public static function generateSalt()
    // {
    //     return md5(uniqid(rand(), true));
    // }
}
