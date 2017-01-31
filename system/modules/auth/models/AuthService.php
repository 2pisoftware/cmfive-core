<?php

class AuthService extends DbService {

    public $_roles;
    public $_roles_loaded = false;
    public $_rest_user = null;
	private static $_cache = array();

    function login($login, $password, $client_timezone, $skip_session = false) {
        // $password = User::encryptPassword($password);
        // $user_data = $this->_db->get("user")->where("login", $login)->and("password", $password)->and("is_active", "1")->and("is_deleted", "0")->fetch_row();
        $user = $this->getUserForLogin($login);
        if (empty($user->id) || ($user->encryptPassword($password) !== $user->password) || $user->is_external == 1) {
            return null;
        }
        // if ($user_data != null) {
        // $user = new User($this->w);
        // $user->fill($user_data);
        $user->updateLastLogin();
        if (!$skip_session) {
            $this->w->session('user_id', $user->id);
            $this->w->session('timezone', $client_timezone);
        }
        return $user;
        // } else {
        //     return null;
        // }
    }

    function forceLogin($user_id = null) {
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

    function __init() {
        $this->_loadRoles();
    }

    function loggedIn() {
        return $this->w->session('user_id');
    }

    function getUserForLogin($login) {
        $user = $this->db->get("user")->where("login", $login)->and("is_deleted", 0)->fetch_row();
		return $this->getObjectFromRow("User", $user);
    }

    function getUserForToken($token) {
        return $this->getObject("User", array("password_reset_token" => $token));
    }

    function setRestUser($user) {
        $this->_rest_user = $user;
    }

    /**
     * Return the logged in user based on the session variable user_id.
     * 
     * If a user has been set from a REST service, then that user will
     * be returned.
     * 
     * @return User|NULL
     */
    function user() {
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
    function hasRole($role) {
        return $this->user() ? $this->user()->hasRole($role) : false;
    }

	/**
     * 
     * Check if the current user can access the specified path
     * @ return false if the login user is not allowed access to this path
     *  OR return string url if it is provided as a parameter
     */
    function allowed($path, $url = null) {
		$key = $path.'::'.$url;
		if(!empty(self::$_cache[$key])) {
			return self::$_cache[$key];
		}
        $parts = $this->w->parseUrl($path);
        if (!in_array($parts['module'], $this->w->modules())) {
            $this->Log->error("Denied access: module '". urlencode($parts['module']). "' doesn't exist");
			self::$_cache[$key] = false;
            return false;
        }

        if ((function_exists("anonymous_allowed") && anonymous_allowed($this->w, $path)) || 
        	($this->user() && $this->user()->allowed($path))) {
			self::$_cache[$key] = $url ? $url : true;
        	return self::$_cache[$key];
        }
        self::$_cache[$key] = false;
        return false;
    }

    /**
     * Return an array of role names for all available roles
     *
     * @return array of strings
     */
    function getAllRoles() {
        $this->_loadRoles();
        if (!$this->_roles) {
            $roles = array();

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

    function _loadRoles() {
        // do this only once
        if ($this->_roles_loaded)
            return;

        $modules = $this->w->modules();
        foreach ($modules as $model) {
            $file = $this->w->getModuleDir($model) . $model . ".roles.php";
            if (file_exists($file)) {
                require_once $file;
            }
        }
        $this->_roles_loaded = true;
    }

    function getUser($id) {
        return $this->getObject("User", $id);
    }

    function getUsersAndGroups($includeDeleted = false) {
    	$where = array();
        $where["is_active"] = 1;
    	if (!$includeDeleted) {
    		$where["is_deleted"]=0;
    	}
        return $this->getObjects("User", $where, true);
    }

    function getUsers($includeDeleted = false) {
        $where = array();
        $where["is_group"]=0;
        $where['is_active'] = 1;
    	if (!$includeDeleted) {
    		$where["is_deleted"]=0;
    	}
    	return $this->getObjects("User", $where, true);
    }
    
    function getUserForContact($cid) {
        return $this->getObject("User", array("contact_id" => $cid));
    }

    function getUsersForRole($role) {
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

    function getGroups() {
        $rows = $this->_db->get("user")->where(array('is_active' => 1, 'is_deleted' => 0, 'is_group' => 1))->fetch_all();

        if ($rows) {
            $objects = $this->fillObjects("User", $rows);

            return $objects;
        }
        return null;
    }

    function getGroupMembers($group_id = null, $user_id = null) {
        if ($group_id)
            $option['group_id'] = $group_id;

        if ($user_id)
            $option['user_id'] = $user_id;

        $groupMembers = $this->getObjects("GroupUser", $option, true);

        if ($groupMembers) {
            return $groupMembers;
        }
        return null;
    }

    function getGroupMemberById($id) {
        $groupMember = $this->getObject("GroupUser", $id);

        if ($groupMember) {
            return $groupMember;
        }
        return null;
    }

    function getRoleForLoginUser($group_id, $user_id) {
        $groupMember = $this->getObject("GroupUser", array('group_id' => $group_id, 'user_id' => $user_id));

        if ($groupMember) {
            return $groupMember->role;
        }
        return null;
    }

}
