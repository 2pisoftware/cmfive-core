<?php

namespace System\Modules\Auth;

function removeUser(\Web $w, $params = [])
{
    $user = $params['user'];
    $redirect = $params['redirect'];

    $user_group_member = $w->db->get("group_user")->where("user_id", $user->id)->fetchAll();

    $w->ctx("user_group_member", $user_group_member);
    $w->ctx("user", $user);
    $w->ctx("redirect", $redirect);
}
