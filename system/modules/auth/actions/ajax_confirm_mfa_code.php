<?php

use \Sonata\GoogleAuthenticator\GoogleAuthenticator;

function ajax_confirm_mfa_code_POST(Web $w)
{
    $w->setLayout(null);

    $request_data = json_decode(file_get_contents("php://input"), true);
    if (empty($request_data) || empty($request_data["id"]) || empty($request_data["mfa_code"])) {
        $w->out((new AxiosResponse())->setErrorResponse("Request data missing", null));
        return;
    }

    $user = $w->Auth->getUser($request_data["id"]);
    if (empty($user)) {
        $w->out((new AxiosResponse())->setErrorResponse("Unable to find user", null));
        return;
    }

    if (!(new GoogleAuthenticator())->checkCode($user->mfa_secret, $request_data["mfa_code"])) {
        $w->out((new AxiosResponse())->setErrorResponse("Incorrect MFA Code", null));
        return;
    }

    $user->is_mfa_enabled = true;

    if (!$user->update()) {
        $w->out((new AxiosResponse())->setErrorResponse("Failed to confirm MFA code", null));
        return;
    }

    $w->out((new AxiosResponse())->setSuccessfulResponse("MFA enabled", null));
}
