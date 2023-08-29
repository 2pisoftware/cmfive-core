<?php

/**
 * Add new members to a group
 *
 * @param <type> $w
 */
function groupmember_GET(Web $w)
{
    $option = $w->pathMatch("group_id");

    $users = AuthService::getInstance($w)->getUsersAndGroups();

    $select = [0 => [], 1 => []];
    foreach ($users as $user) {
        // We do not list ourselves as an option 
        if ($user->id != $option["group_id"]) {
            $name = $user->is_group == 1 ? strtoupper($user->login) : $user->getContact()->getFullName();
            $select[!empty($user->is_group)][$name] = array($name, $user->id);
        }
    }
    // Sort ignoring case
    ksort($select[0], SORT_STRING | SORT_FLAG_CASE);
    ksort($select[1], SORT_STRING | SORT_FLAG_CASE);

    $template['New Member'] = [[["Select Member: ", "select", "member_id", null, $select[0] + $select[1]]]];
    if (AuthService::getInstance($w)->user()->is_admin) {
        $template['New Member'][0][] = array("Owner", "checkbox", "is_owner");
    }

    $validation = ['member_id' => ['required']];

    $w->out(HtmlBootstrap5::multiColForm($template, "/admin/groupmember/" . $option['group_id'], "POST", "Save", null, null, null, "_self", true, $validation));

    $w->setLayout(null);
}

function groupmember_POST(Web $w)
{
    $p = $w->pathMatch("group_id");
    $member_id = Request::int('member_id');
    $group_id = $p['group_id'];
    $is_owner = Request::bool('is_owner');
    $exceptions = array();
    // store all parent groups in session
    $groupUsers = AuthService::getInstance($w)->getUser($group_id)->isInGroups();
    if ($groupUsers) {
        foreach ($groupUsers as $groupUser) {
            $groupUser->getParents();
        }
    }

    // add member to the group only if it isn't already in there
    // this logic should move to the model!
    $existUser = AuthService::getInstance($w)->getUser($member_id)->isInGroups($group_id);
    if (!$existUser) {
        if (!$w->session('parents') || !in_array($member_id, $w->session('parents'))) {
            $groupMember = new GroupUser($w);
            $groupMember->group_id = $group_id;
            $groupMember->user_id = $member_id;
            $groupMember->role = ($is_owner && $is_owner == 1) ? "owner" : "member";
            $groupMember->insert();
        }

        if ($w->session('parents') && in_array($member_id, $w->session('parents'))) {
            $exceptions[] = AuthService::getInstance($w)->getUser($member_id)->login;
        }
    } else {
        /** @var User */
        $user = $existUser[0]->getUser();

        $exceptions[] = $user->is_group == 1 ? $user->login : $user->getContact()->getFullName();
    }

    $w->sessionUnset('parents');

    if (!empty($exceptions)) {
        $w->error(implode(", ", $exceptions) . " can not be added!", "/admin/moreInfo/" . $group_id);
    } else {
        $w->msg("New member added", "/admin/moreInfo/" . $group_id);
    }
}
