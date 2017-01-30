<?php
function groupdelete_GET(Web &$w)
{
	$option = $w->pathMatch("group_id");

	$user = $w->Auth->getUser($option['group_id']);
	$user->delete();

	$roles = $user->getRoles();

	foreach ($roles as $role)
	{
		$user->removeRole($role);
	}
	$members = $w->Auth->getGroupMembers($option['group_id']);

	if ($members)
	{
		foreach ($members as $member)
		{
			$member->delete();
		}
	}
	$w->msg(__("Group is deleted!"), "/admin/groups");
}
