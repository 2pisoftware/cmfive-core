<?php

function ajax_update_security_details_POST(Web $w)
{
    $w->setLayout(null);

    $request_data = json_decode(file_get_contents("php://input"), true);
    if (empty($request_data) || empty($request_data["id"]) || empty($request_data["login"])) {
        $w->out((new AxiosResponse())->setErrorResponse("Request data missing", null));
        return;
    }

    $user = $w->Auth->getUser($request_data["id"]);
    if (empty($user)) {
        $w->out((new AxiosResponse())->setErrorResponse("Unable to find user", null));
        return;
    }

    $user->fill($request_data["security_details"]);

    if (!$user->insertOrUpdate()) {
        $w->out((new AxiosResponse())->setErrorResponse("Failed to update details", null));
        return;
    }

    $w->out((new AxiosResponse())->setSuccessfulResponse("User details updated", null));
}
