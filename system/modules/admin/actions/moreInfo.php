<?php

/**
 * Display member and permission infomation
 *
 * @param <type> $w
 */
function moreInfo_GET(Web &$w)
{
	$w->setLayout('layout-bootstrap-5');

	$option = $w->pathMatch("group_id");

	AdminService::getInstance($w)->navigation($w, AuthService::getInstance($w)->getUser($option['group_id'])->login);

	if (AuthService::getInstance($w)->user()->is_admin || AuthService::getInstance($w)->getRoleForLoginUser($option['group_id'], AuthService::getInstance($w)->user()->id) == "owner") {
		$w->ctx("addMember", HtmlBootstrap5::box("/admin/groupmember/" . $option['group_id'], "New Member", true, false, null, null, 'isbox', null, "btn btn-sm btn-primary"));
	}
	$w->ctx("editPermission", HtmlBootstrap5::b("/admin/permissionedit/" . $option['group_id'], "Edit Permissions", null, null, false, "btn btn-sm btn-primary"));
	//fill in member table;
	$table = array(array("Name", "Role", "Operations", "sort_key" => null));

	$groupMembers = AuthService::getInstance($w)->getGroupMembers($option['group_id']);

	if ($groupMembers) {
		foreach ($groupMembers as $groupMember) {
			$line = array();

			$style = $groupMember->role == "owner" ? "<div class='text-primary'>" : "<div>";

			$name = $groupMember->getUser()->is_group == 1 ? $groupMember->getUser()->login : $groupMember->getUser()->getContact()->getFullName();

			$line[] = $style . $name . "</div>";
			$line[] = $style . $groupMember->role . "</div>";

			if (AuthService::getInstance($w)->user()->is_admin || AuthService::getInstance($w)->getRoleForLoginUser($option['group_id'], AuthService::getInstance($w)->user()->id) == "owner") {
				$line[] = HtmlBootstrap5::b("/admin/memberdelete/" . $option['group_id'] . "/" . $groupMember->id, "Delete", "Are you sure you want to delete this group?", "deletebutton", false, "btn-sm btn-danger");
			} else {
				$line[] = null;
			}
			$line["sort_key"] = strtoupper($name);
			$table[] = $line;
		}
	}
	// Order by sort key (name/group in uppercase)
	array_multisort(
		array_column($table, "sort_key"),
		SORT_ASC,
		$table
	);
	// Remove sort column
	for ($i = 0, $length = count($table); $i < $length; ++$i) {
		unset($table[$i]["sort_key"]);
	}

	$w->ctx("memberList", HtmlBootstrap5::table($table, null, "tablesorter", true));
}
