<?php
/**
* Add new members to a group
*
* @param <type> $w
*/
function groupmember_GET(Web $w)
{
	$option = $w->pathMatch("group_id");

	$users = $w->Auth->getUsersAndGroups();

	$select = [0 => [], 1 => []];
	foreach ($users as $user) {
            // We do not list ourselves as an option 
            if ($user->id != $option["group_id"]) {
		$name = $user->is_group == 1 ? strtoupper($user->login) : $user->getContact()->getFullName();
		$select[!empty($user->is_group)][$name] = array($name, $user->id);
            }
	}
	
	ksort($select[0]);
	ksort($select[1]);

	$template['New Member'] = [[[__("Select Member: "), "select", "member_id", null, $select[0] + $select[1]]]];
	if ($w->Auth->user()->is_admin) {
		$template['New Member'][0][] = array(__("Owner"),"checkbox","is_owner");
	}
		
	$w->out(Html::multiColForm($template,"/admin/groupmember/".$option['group_id'],"POST",__("Save")));

	$w->setLayout(null);
}

function groupmember_POST(Web $w)
{
	$p = $w->pathMatch("group_id");
	$member_id = $w->request('member_id');
	$group_id = $p['group_id'];
	$is_owner = $w->request('is_owner');
	$exceptions = array();
	// store all parent groups in session
	$groupUsers = $w->Auth->getUser($group_id)->isInGroups();
	if ($groupUsers)
	{
		foreach ($groupUsers as $groupUser)
		{
			$groupUser->getParents();
		}
	}

	// add member to the group only if it isn't already in there
	// this logic should move to the model!
	$existUser = $w->Auth->getUser($member_id)->isInGroups($group_id);
	if (!$existUser)
	{
		if (!$w->session('parents') || !in_array($member_id, $w->session('parents')))
		{
			$groupMember = new GroupUser($w);
			$groupMember->group_id = $group_id;
			$groupMember->user_id = $member_id;
			$groupMember->role = ($is_owner && $is_owner == 1) ? "owner" : "member";
			$groupMember->insert();
		}
			
		if ($w->session('parents') && in_array($member_id, $w->session('parents')))
		{
			$exceptions[] = $w->Auth->getUser($member_id)->login;
		}
	}
	else
	{
		$user = $existUser[0]->getUser();
			
		$exceptions[] = $user->is_group == 1 ? $user->login : $user->getContact()->getFullName();
	}

	$w->sessionUnset('parents');

	if (!empty($exceptions)) {
		$w->error(implode(", ", $exceptions).__(" can not be added!"), "/admin/moreInfo/".$group_id);
	} else {
		$w->msg(__("New members are added!"), "/admin/moreInfo/".$group_id);
	}
}
