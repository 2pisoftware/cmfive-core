<?php

/**
 * User object
 *
 * @author Carsten Eckelmann, May 2014
 *
 */
class User extends DbObject {

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
	
    public function checkPassword($password) {
    	if (empty($this->password) || empty($this->password_salt) || empty($password)) {
    		return false;
    	}
    	
    	return $this->password == $this->encryptPassword($password);
    }

	public function getLanguage() {
		return $this->$language;
	}

	public function getAvailableLanguages() {
		return [[__("English"), "en_US.UTF-8"], [__("German"), "de_DE.UTF-8"], [__("French"), "fr_FR.UTF-8"], [__("Chinese"), "zh_CN.UTF-8"], [__("Japanese"), "ja_JP.UTF-8"], [__("Spanish"), "es_ES.UTF-8"], [__("Dutch"), "nl_NL.UTF-8"], [__("Russian"), "ru_RU.UTF-8"], [__("Gaelic"), "gd_GB.UTF-8"]];
	}

	public function delete($force = false) {

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
		}
	}

	public function getContact() {
		if (!$this->_contact) {
			$this->_contact = $this->getObject("Contact", $this->contact_id);
		}
		return $this->_contact;
	}

	/**
	 * @param integer $group_id
	 * @return true if this user is in the group with the id
	 */
	public function isInGroups($group_id = null) {
		$groupUsers = isset($group_id) ? $this->getObjects("GroupUser", array(
			'user_id' => $this->id,
			'group_id' => $group_id,
		)) : $this->getObjects("GroupUser", array(
			'user_id' => $this->id,
		));

		if ($groupUsers) {
			return $groupUsers;
		}
		return null;
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
	public function inGroup(User $group) {
		$groupmembers = $this->Auth->getGroupMembers($group->id, null);

		if ($groupmembers) {
			foreach ($groupmembers as $member) {
				if ($member->user_id == $this->id) {
					return true;
				}

				$usr = $this->Auth->getUser($member->user_id);
				if (!empty($usr) && $usr->is_group == 1 && $this->inGroup($usr)) {
					return true;
				}
			}
		}
	}

	public function getFirstName() {
		$contact = $this->getContact();

		if ($contact) {
			$name = $contact->getFirstName();
		}
		return $name;
	}

	public function getSurname() {
		$contact = $this->getContact();
		if ($contact) {
			$name = $contact->getSurname();
		}
		return $name;
	}

	public function getFullName() {
		$contact = $this->getContact();
		$name = ucfirst($this->login);
		if ($contact) {
			$name = $contact->getFullName();
		}
		return $name;
	}

	public function getSelectOptionTitle() {
		return $this->getFullName();
	}

	public function getSelectOptionValue() {
		return $this->id;
	}

	/**
	 * @return string, either the login or first name
	 */
	public function getShortName() {
		$contact = $this->getContact();
		$name = ucfirst($this->login);
		if ($contact) {
			$name = $contact->firstname;
		}
		return $name;
	}

	/**
	 * @param string $force
	 * @return string array of all roles that this user has
	 */
	public function getRoles($force = false) {
		if ($this->is_admin) {
			return $this->Auth->getAllRoles();
		}
		if (!$this->_roles || $force) {
			$this->_roles = array();

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
			$rows = $this->getObjects("UserRole", array("user_id" => $this->id), true);

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
	public function updateLastLogin() {
		$data = array(
			"dt_lastlogin" => $this->time2Dt(time()),
		);
		$this->_db->update("user", $data)->where("id", $this->id)->execute();
	}

	/**
	 * Check whether a user has this role
	 *
	 * @param string $role
	 * @return true if and only if the user has this role
	 */
	public function hasRole($role) {
		if ($this->is_admin) {
			return true;
		}
		if ($this->getRoles()) {
			return in_array($role, $this->_roles);
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
	public function hasAnyRole($roles) {
		if ($this->is_admin) {
			return true;
		}
		if (!empty($roles)) {
			if (is_array($roles)) {
				foreach ($roles as $r) {
					if ($this->hasRole($r)) {
						return true;
					}
				}
			} else if (is_string($roles)) {
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
	public function addRole($role) {
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
	public function removeRole($role) {
		if ($this->hasRole($role)) {
			$role = $this->admin->getObject("UserRole", ["user_id" => $this->id, "role" => $role]);
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
	public function allowed($path) {
		if (!$this->is_active) {
			return false;
		}
		if ($this->is_admin) {
			return true;
		}
		if ($this->getRoles()) {
			foreach ($this->getRoles() as $rn) {
				$rolefunc = "role_" . $rn . "_allowed";
				if (function_exists($rolefunc)) {
					if ($rolefunc($this->w, $path)) {
						return true;
					}
				} else {
					$this->w->Log->error("Role '" . $rn . "' does not exist!");
				}
			}
		}
		return false;
	}

	/**
	 * Encrypt the password using sha1 and a user unique salt.
	 *
	 * @param string $password
	 * @return string
	 */
	public function encryptPassword($password) {
		if (empty($this->password_salt)) {
			// Salt hash is generated per user
			$this->password_salt = md5(uniqid(rand(), TRUE));
			$this->update();
		}
		return sha1($this->password_salt . $password);
	}

	/**
	 * set the user's password and encrypt it using sha1 and the user's salt
	 *
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->password = $this->encryptPassword($password);
		$this->w->callHook('auth', 'setpassword', [$password, $this]);
	}

	public static function generateSalt() {
		return md5(uniqid(rand(), TRUE));
	}

}
