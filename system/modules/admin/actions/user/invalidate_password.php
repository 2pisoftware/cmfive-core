<?php

function invalidate_password_ALL(Web $w)
{
    list($user_id) = $w->pathMatch("user_id");
    if (empty($user_id)) {
        $w->error("User not found", "/admin/users");
    }

    $user = AuthService::getInstance($w)->getUser($user_id);
    if (empty($user)) {
        $w->error("User not found", "/admin/users");
    }

    $user->is_password_invalid = true;
    $user->update();

    $w->msg("User's password successfully invalidated", "/admin/users");
}
