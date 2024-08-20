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
        $w->ctx("addMember", Html::box("/admin/groupmember/" . $option['group_id'], "New Member", true));
    }
    $w->ctx("editPermission", Html::b("/admin/permissionedit/" . $option['group_id'], "Edit Permissions"));

    //fill in member table;
    $table = [["Name", "Role", "Operations"]];

    $groupMembers = AuthService::getInstance($w)->getGroupMembers($option['group_id']);

    if ($groupMembers) {
        usort($groupMembers, function ($a, $b) {
            $user_a = $a->getUser();
            $user_b = $b->getUser();
            $compare_a = $user_a->is_group == 1 ? $user_a->login : $user_a->getFullName();
            $compare_b = $user_b->is_group == 1 ? $user_b->login : $user_b->getFullName();
            return strcasecmp($compare_a, $compare_b);
        });

        foreach ($groupMembers as $groupMember) {
            $style = $groupMember->role == "owner" ? "<div class='text-primary'>" : "<div>";

            $line = [
                $style . $groupMember->getUser()->is_group == 1 ? $groupMember->getUser()->login : $groupMember->getUser()->getFullName() . "</div>",
                $style . $groupMember->role . "</div>"
            ];

            if (AuthService::getInstance($w)->user()->is_admin || AuthService::getInstance($w)->getRoleForLoginUser($option['group_id'], AuthService::getInstance($w)->user()->id) == "owner") {
                $line[] = Html::b(
                    href: "/admin/memberdelete/" . $option['group_id'] . "/" . $groupMember->id,
                    title: "Delete",
                    confirm: "Are you sure you want to delete this member?",
                    class: "btn-danger btn-sm"
                );
            } else {
                $line[] = null;
            }
            $table[] = $line;
        }
    }

    $w->ctx("memberList", Html::table($table, null, "tablesorter", true));
}
