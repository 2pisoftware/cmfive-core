<?php
/**
* Display a list of all groups which are not deleted
*
* @param <type> $w
*/
function index_GET(Web &$w)
{
	AdminService::getInstance($w)->navigation($w,"Groups");

	$table = array(array("Title","Parent Groups","Operations"));

	$groups = AuthService::getInstance($w)->getGroups();

	if ($groups) {
            foreach ($groups as $group) {
                $ancestors = array();

                $line = array();

                $line[] = AuthService::getInstance($w)->user()->is_admin ? HtmlBootstrap5::box($w->localUrl("/admin/groupedit/".$group->id),"<u>".$group->login."</u>") : $group->login;
                //if it is a sub group from other group;
                $groupUsers = $group->isInGroups();

                if ($groupUsers) {
                    foreach ($groupUsers as $groupUser) {
                        $ancestors[] = $groupUser->getGroup()->login;
                    }
                }
                $line[] = count($ancestors) > 0 ? "<div style=\"color:green;\">".implode(", ", $ancestors)."</div>" : "";

                $operations = HtmlBootstrap5::b("/admin-groups/edit/".$group->id,"Edit");

                if (AuthService::getInstance($w)->user()->is_admin)
                $operations .= HtmlBootstrap5::b("/admin-groups/delete/".$group->id,"Delete","Are you sure you want to delete this group?");

                $line[] = $operations;

                $table[] = $line;
            }
	}

	if (AuthService::getInstance($w)->user()->is_admin) {
            $w->out(HtmlBootstrap5::box("/admin/groupadd", "New Group", true));
	}
        
	$w->out(HtmlBootstrap5::table($table,null,"tablesorter",true));
}
