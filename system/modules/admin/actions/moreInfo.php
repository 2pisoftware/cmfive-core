<?php
/**
* Display member and permission infomation
*
* @param <type> $w
*/
function moreInfo_GET(Web &$w)
{
	$option = $w->pathMatch("group_id");

	AdminService::getInstance($w)->navigation($w, AuthService::getInstance($w)->getUser($option['group_id'])->login);

	if (AuthService::getInstance($w)->user()->is_admin || AuthService::getInstance($w)->getRoleForLoginUser($option['group_id'], AuthService::getInstance($w)->user()->id) == "owner")
	{
		$w->ctx("addMember", Html::box("/admin/groupmember/".$option['group_id'],"New Member",true));
	}
	$w->ctx("editPermission", Html::b("/admin/permissionedit/".$option['group_id'],"Edit Permissions"));

	//fill in member table;
	$table = array(array("Name","Role","Operations"));

	$groupMembers = AuthService::getInstance($w)->getGroupMembers($option['group_id']);
		
	if ($groupMembers)
	{
		foreach ($groupMembers as $groupMember)
		{
			$line = array();
				
			$style = $groupMember->role == "owner" ? "<div style=\"color:red;\">" : "<div style=\"color:blue;\">";
				
			$name = $groupMember->getUser()->is_group == 1 ? $groupMember->getUser()->login : $groupMember->getUser()->getContact()->getFullName();
				
			$line[] = $style.$name."</div>";
			$line[] = $style.$groupMember->role."</div>";
				
			if (AuthService::getInstance($w)->user()->is_admin || AuthService::getInstance($w)->getRoleForLoginUser($option['group_id'], AuthService::getInstance($w)->user()->id) == "owner")
			{
				$line[] = Html::a("/admin/memberdelete/".$option['group_id']."/".$groupMember->id,"Delete",null,null,"Are you sure you want to delete this member?");
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
