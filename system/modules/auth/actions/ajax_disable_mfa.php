<?php

function ajax_disable_mfa_POST(Web $w)
{
    $w->setLayout(null);

    $request_data = json_decode(file_get_contents("php://input"), true);
    if (empty($request_data) || empty($request_data["id"])) {
        $w->out((new JsonResponse())->setErrorResponse("Request data missing", null));
        return;
    }

    $user = AuthService::getInstance($w)->getUser($request_data["id"]);
    if (empty($user)) {
        $w->out((new JsonResponse())->setErrorResponse("Unable to find user", null));
        return;
    }

    $user->is_mfa_enabled = false;
    $user->mfa_secret = null;

    if (!$user->update(true)) {
        $w->out((new JsonResponse())->setErrorResponse("Failed to disable MFA", null));
        return;
    }

    $w->out((new JsonResponse())->setSuccessfulResponse("MFA disabled", null));
}
