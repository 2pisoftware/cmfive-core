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

    $table = [["Title", "Parent Groups", "Operations"]];

    $groups = AuthService::getInstance($w)->getGroups();

    if ($groups) {
        usort($groups, function ($a, $b) {
            return strcasecmp($a->login, $b->login);
        });

        foreach ($groups as $group) {
            $ancestors = [];

            $line = [AuthService::getInstance($w)->user()->is_admin ? Html::box($w->localUrl("/admin/groupedit/" . $group->id), "<u>" . $group->login . "</u>") : $group->login];
            //if it is a sub group from other group;
            $groupUsers = $group->isInGroups();

            if ($groupUsers) {
                foreach ($groupUsers as $groupUser) {
                    $ancestors[] = $groupUser->getGroup()->login;
                }
            }
            $line[] = count($ancestors) > 0 ? "<div class='text-success'>" . implode(", ", $ancestors) . "</div>" : "";

            $operations = HtmlBootstrap5::b("/admin/moreInfo/" . $group->id, "Edit", null, null, false, "btn btn-sm btn-primary");

            if (AuthService::getInstance($w)->user()->is_admin) {
                $operations .= HtmlBootstrap5::b("/admin/groupdelete/" . $group->id, "Delete", "Are you sure you want to delete this group?", null, false, "btn btn-sm btn-danger");
            }

            $line[] = HtmlBootstrap5::buttonGroup($operations);
            $table[] = $line;
        }
    }

    if (AuthService::getInstance($w)->user()->is_admin) {
        $w->out(Html::box("/admin/groupadd", "New Group", true));
    }

    $w->out(HtmlBootstrap5::table($table, null, "tablesorter"));
}
