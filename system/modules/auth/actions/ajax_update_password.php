<?php

function ajax_update_password_POST(Web $w)
{
    $w->setLayout(null);

    $request_data = json_decode(file_get_contents("php://input"), true);
    if (empty($request_data) || empty($request_data["id"]) || empty($request_data["new_password"]) || empty($request_data["repeat_new_password"])) {
        $w->out((new AxiosResponse())->setErrorResponse("Request data missing", null));
        return;
    }

    if ($request_data["new_password"] !== $request_data["repeat_new_password"]) {
        $w->out((new AxiosResponse())->setErrorResponse("Passwords don't match", null));
        return;
    }

    $user = $w->Auth->getUser($request_data["id"]);
    if (empty($user)) {
        $w->out((new AxiosResponse())->setErrorResponse("Unable to find user", null));
        return;
    }

    $user->setPassword($request_data["new_password"]);

    if (!$user->insertOrUpdate()) {
        $w->out((new AxiosResponse())->setErrorResponse("Failed to update password", null));
        return;
    }

    $w->out((new AxiosResponse())->setSuccessfulResponse("User password updated", null));
}
