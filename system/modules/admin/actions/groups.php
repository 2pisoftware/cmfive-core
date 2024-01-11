<?php

/**
 * Display a list of all groups which are not deleted
 *
 * @param <type> $w
 */
function groups_GET(Web &$w)
{

	$w->setLayout('layout-bootstrap-5');

	AdminService::getInstance($w)->navigation($w, "Groups");

	$table = array(array("Title", "Parent Groups", "Actions"));
	$table = array(array("Title", "Parent Groups", "Operations", "sort_key" => null));

	$groups = AuthService::getInstance($w)->getGroups();

	if ($groups) {
		foreach ($groups as $group) {
			$ancestors = array();

			$line = array();
			// Use box but set $button=false to remove button styling
			//$line[] = AuthService::getInstance($w)->user()->is_admin ? HtmlBootstrap5::a("/admin/groupedit/" . $group->id, $group->login) : $group->login;
			$line[] = AuthService::getInstance($w)->user()->is_admin ? HtmlBootstrap5::box("/admin/groupedit/" . $group->id, $group->login, false, false, null, null, 'isbox', null, " link-primary cursor-pointer ") : $group->login;
			//if it is a sub group from other group;
			$groupUsers = $group->isInGroups();

			if ($groupUsers) {
				foreach ($groupUsers as $groupUser) {
					$ancestors[] = $groupUser->getGroup()->login;
				}
			}
			$line[] = count($ancestors) > 0 ? "<div style=\"color:green;\">" . implode(", ", $ancestors) . "</div>" : "";

			$buttonGroup = HtmlBootstrap5::b("/admin/moreInfo/" . $group->id, "Edit", null, "editbutton", false, 'btn-sm btn-secondary');
			if (AuthService::getInstance($w)->user()->is_admin) {
				$buttonGroup .= HtmlBootstrap5::b("/admin/groupdelete/" . $group->id, "Delete", "Are you sure you want to delete this group?", "deletebutton", false, "btn-sm btn-danger");
			}
			$operations = HtmlBootstrap5::buttonGroup($buttonGroup);

			$line[] = $operations;

			$line["sort_key"] = strtoupper($group->login);

			$table[] = $line;
		}
	}

	if (AuthService::getInstance($w)->user()->is_admin) {
		//$w->out(HtmlBootstrap5::box("/admin/groupadd", "New Group", true, false, null, null, 'isbox', null, 'btn btn-sm btn-primary'));
		$w->ctx("button", HtmlBootstrap5::box("/admin/groupadd", "New Group", true, false, null, null, 'isbox', null, 'btn btn-sm btn-primary'));
	}

	// Order by sort key (group name in uppercase)
	array_multisort(
		array_column($table, "sort_key"),
		SORT_ASC,
		$table
	);
	// Remove sort column
	for ($i = 0, $length = count($table); $i < $length; ++$i) {
		unset($table[$i]["sort_key"]);
	}

	//$w->out(HtmlBootstrap5::table($table, null, "tablesorter", true));
	$w->ctx("table", HtmlBootstrap5::table($table, null, "tablesorter", true));
}
