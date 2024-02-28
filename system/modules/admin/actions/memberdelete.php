<?php
function memberdelete_GET(Web &$w)
{
	$option = $w->pathMatch("group_id","member_id");

	$member = AuthService::getInstance($w)->getGroupMemberById($option['member_id']);

	if ($member)
	{
		$member->delete();
	}
	$w->msg("Member deleted", "/admin/moreInfo/".$option['group_id']);
}
