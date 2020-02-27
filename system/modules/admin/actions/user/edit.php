<?php

function edit_GET(Web $w)
{
    $redirect_url = "/admin/users";

    list($user_id) = $w->pathMatch("id");
    if (empty($user_id)) {
        $w->error("Unable to find User", $redirect_url);
    }

    $user = $w->Auth->getUser($user_id);
    if (empty($user)) {
        $w->error("Unable to find User", $redirect_url);
    }

    $w->ctx("user", $user);
}
