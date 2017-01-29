<?php
/**
* Display a list of all groups which are not deleted
*
* @param <type> $w
*/
function index_GET(Web &$w)
{
	$w->Admin->navigation($w,"Groups");

	$table = array(array("Title","Parent Groups","Operations"));

	$groups = $w->Auth->getGroups();

	if ($groups) {
            foreach ($groups as $group) {
                $ancestors = array();

                $line = array();

                $line[] = $w->Auth->user()->is_admin ? Html::box($w->localUrl("/admin/groupedit/".$group->id),"<u>".$group->login."</u>") : $group->login;
                //if it is a sub group from other group;
                $groupUsers = $group->isInGroups();

                if ($groupUsers) {
                    foreach ($groupUsers as $groupUser) {
                        $ancestors[] = $groupUser->getGroup()->login;
                    }
                }
                $line[] = count($ancestors) > 0 ? "<div style=\"color:green;\">".implode(", ", $ancestors)."</div>" : "";

                $operations = Html::b("/admin-groups/edit/".$group->id,"Edit");

                if ($w->Auth->user()->is_admin)
                $operations .= Html::b("/admin-groups/delete/".$group->id,"Delete","Are you sure you want to delete this group?");

                $line[] = $operations;

                $table[] = $line;
            }
	}

	if ($w->Auth->user()->is_admin) {
            $w->out(Html::box("/admin/groupadd", "New Group", true));
	}
        
	$w->out(Html::table($table,null,"tablesorter",true));
}
