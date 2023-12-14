<?php
function groupdelete_GET(Web &$w)
{
	$option = $w->pathMatch("group_id");

	$user = AuthService::getInstance($w)->getUser($option['group_id']);
	$user->delete();

	$roles = $user->getRoles();

	foreach ($roles as $role)
	{
		$user->removeRole($role);
	}
	$members = AuthService::getInstance($w)->getGroupMembers($option['group_id']);

	if ($members)
	{
		foreach ($members as $member)
		{
			$member->delete();
		}
	}
	$w->msg("Group deleted", "/admin/groups");
}
