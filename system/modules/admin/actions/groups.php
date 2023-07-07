<?php
/**
* Display a list of all groups which are not deleted
*
* @param <type> $w
*/
function groups_GET(Web &$w)
{
	AdminService::getInstance($w)->navigation($w,"Groups");

	$table = array(array("Title", "Parent Groups", "Operations", "sort_key" => null));

	$groups = AuthService::getInstance($w)->getGroups();

	if ($groups)
	{
		foreach ($groups as $group)
		{
			$ancestors = array();
			 
			$line = array();

			$line[] = AuthService::getInstance($w)->user()->is_admin ? Html::box($w->localUrl("/admin/groupedit/".$group->id),"<u>".$group->login."</u>") : $group->login;
			//if it is a sub group from other group;
			$groupUsers = $group->isInGroups();

			if ($groupUsers)
			{
				foreach ($groupUsers as $groupUser)
				{
					$ancestors[] = $groupUser->getGroup()->login;
				}
			}
			$line[] = count($ancestors) > 0 ? "<div style=\"color:green;\">".implode(", ", $ancestors)."</div>" : "";

			$operations = Html::b("/admin/moreInfo/".$group->id,"Edit");
			 
			if (AuthService::getInstance($w)->user()->is_admin)
			$operations .= Html::b("/admin/groupdelete/".$group->id,"Delete","Are you sure you want to delete this group?");

			$line[] = $operations;

			$line["sort_key"] = strtoupper($group->login);
			 
			$table[] = $line;
		}
	}

	if (AuthService::getInstance($w)->user()->is_admin)
	{
		$w->out(Html::box("/admin/groupadd", "New Group", true));
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

	$w->out(Html::table($table,null,"tablesorter",true));
}
