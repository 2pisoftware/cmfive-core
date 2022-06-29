<?php

function invalidate_all_passwords_ALL(Web $w)
{
    $users = AuthService::getInstance($w)->getUsers();
    if (empty($users)) {
        $w->error("Users not found", "/admin/users");
    }

    foreach ($users as $user) {
        $user->is_password_invalid = true;
        $user->update();
    }

    $w->redirect("/admin/users");
}
