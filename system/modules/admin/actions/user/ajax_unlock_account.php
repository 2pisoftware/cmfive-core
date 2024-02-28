<?php

function ajax_unlock_account_POST(Web $w)
{
    $w->setLayout(null);

    $request_data = json_decode(file_get_contents("php://input"), true);
    if (empty($request_data) || empty($request_data["id"])) {
        $w->out((new AxiosResponse())->setErrorResponse("Request data missing", null));
        return;
    }

    $user = AuthService::getInstance($w)->getUser($request_data["id"]);
    if (empty($user)) {
        $w->out((new AxiosResponse())->setErrorResponse("Unable to find user", null));
        return;
    }

    if ($user->is_locked) {
        $user->unlock();
    }

    $w->out((new AxiosResponse())->setSuccessfulResponse("OK", null));
}
