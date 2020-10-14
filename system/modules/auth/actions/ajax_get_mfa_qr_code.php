<?php

use \Sonata\GoogleAuthenticator\GoogleAuthenticator;
use \Sonata\GoogleAuthenticator\GoogleQrUrl;

function ajax_get_mfa_qr_code_GET(Web $w)
{
    $w->setLayout(null);

    $user_id = $w->request("id");
    if (empty($user_id)) {
        $w->out((new AxiosResponse())->setErrorResponse("Request data missing", null));
        return;
    }

    $user = AuthService::getInstance($w)->getUser($user_id);
    if (empty($user)) {
        $w->out((new AxiosResponse())->setErrorResponse("Unable to find user", null));
        return;
    }

    $user->mfa_secret = (new GoogleAuthenticator())->generateSecret();
    $qr_code = GoogleQrUrl::generate(str_replace(" ", "", $user->getFullName()), $user->mfa_secret, str_replace(" ", "", Config::get("main.application_name", "Cmfive")));

    if (!$user->update()) {
        $w->out((new AxiosResponse())->setErrorResponse("Failed to update generate MFA code", null));
        return;
    }

    $w->out((new AxiosResponse())->setSuccessfulResponse("User details updated", ["qr_code" => $qr_code, "mfa_secret" => chunk_split($user->mfa_secret, 4, " ")]));
}
