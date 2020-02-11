<?php

function ajax_is_mfa_enabled_GET(Web $w)
{
    $w->setLayout(null);

    $login = $w->request("login");
    $password = $w->request("password");

    if (empty($login) || empty($password)) {
        $w->out((new AxiosResponse())->setErrorResponse("missing request data", null));
        return;
    }

    $user = $w->Auth->getUserForLogin($login);
    if (empty($user)) {
        $w->out((new AxiosResponse())->setErrorResponse("user not found", null));
        return;
    }

    if (!$user->checkPassword($password)) {
        $w->out((new AxiosResponse())->setErrorResponse("user not found", null));
        return;
    }

    if ($user->is_mfa_enabled && $user->mfa_secret != null) {
        $w->out((new AxiosResponse())->setSuccessfulResponse(null, ["is_mfa_enabled" => true]));
        return;
    }

    $w->out((new AxiosResponse())->setSuccessfulResponse(null, ["is_mfa_enabled" => false]));
}
