<?php
function memberdelete_GET(Web &$w)
{
	$option = $w->pathMatch("group_id","member_id");

	$member = $w->Auth->getGroupMemberById($option['member_id']);

	if ($member)
	{
		$member->delete();
	}
	$w->msg("Member is deleted!", "/admin/moreInfo/".$option['group_id']);
}