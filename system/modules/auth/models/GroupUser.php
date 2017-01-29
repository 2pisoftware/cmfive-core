<?php
class GroupUser extends DbObject
{
	public $group_id;
	public $user_id;
	public $role;
	public $is_active;

	function getGroup()
	{
		$object = $this->getObject("User", $this->group_id);

		if ($object)
		return $object;
		else
		return null;
	}

	function getUser()
	{
		$object = $this->getObject("User", $this->user_id);

		if ($object)
		return $object;
		else
		return null;
	}

	function getGroupRoles()
	{
		$roles = $this->getGroup()->getRoles();

		return $roles;
	}
	/**
	 * Store parents group_user obj into $_SESSION['parents'],
	 * be sure to perform clean up actions after execute this function;
	 **/
	function getParents()
	{
		$_SESSION['parents'][] = $this->group_id;
			
		$parent = $this->getGroup();

		$groupUsers = $parent->isInGroups();

		if ($groupUsers)
		{
			foreach ($groupUsers as $groupUser)
			{
				$groupUser->getParents();
			}
		}
	}
	
	function getDbTableName() {
		return "group_user";
	}
}
