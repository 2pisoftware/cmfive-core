<?php
/**
* Display member and permission infomation
*
* @param <type> $w
*/
function moreInfo_GET(Web &$w)
{
	$option = $w->pathMatch("group_id");

	$w->Admin->navigation($w, $w->Auth->getUser($option['group_id'])->login);

	if ($w->Auth->user()->is_admin || $w->Auth->getRoleForLoginUser($option['group_id'], $w->Auth->user()->id) == "owner")
	{
		$w->ctx("addMember", Html::box("/admin/groupmember/".$option['group_id'],__("New Member"),true));
	}
	$w->ctx("editPermission", Html::b("/admin/permissionedit/".$option['group_id'],__("Edit Permissions")));

	//fill in member table;
	$table = array(array(__("Name"),__("Role"),__("Operations")));

	$groupMembers = $w->Auth->getGroupMembers($option['group_id']);
		
	if ($groupMembers)
	{
		foreach ($groupMembers as $groupMember)
		{
			$line = array();
				
			$style = $groupMember->role == "owner" ? "<div style=\"color:red;\">" : "<div style=\"color:blue;\">";
				
			$name = $groupMember->getUser()->is_group == 1 ? $groupMember->getUser()->login : $groupMember->getUser()->getContact()->getFullName();
				
			$line[] = $style.$name."</div>";
			$line[] = $style.$groupMember->role."</div>";
				
			if ($w->Auth->user()->is_admin || $w->Auth->getRoleForLoginUser($option['group_id'], $w->Auth->user()->id) == "owner")
			{
				$line[] = Html::a("/admin/memberdelete/".$option['group_id']."/".$groupMember->id,__("Delete"),null,null,__("Are you sure you want to delete this member?"));
			}
			else
			{
				$line[] = null;
			}
			$table[] = $line;
		}
	}
	$w->ctx("memberList", Html::table($table,null,"tablesorter",true));
}
