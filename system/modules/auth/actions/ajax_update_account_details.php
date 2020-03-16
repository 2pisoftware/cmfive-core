<?php

function ajax_update_account_details_POST(Web $w)
{
    $w->setLayout(null);

    $request_data = json_decode(file_get_contents("php://input"), true);
    if (empty($request_data)) {
        $w->out((new AxiosResponse())->setErrorResponse("Request data missing", null));
        return;
    }

    $user = $w->Auth->getUser($request_data["id"]);
    if (empty($user)) {
        $w->out((new AxiosResponse())->setErrorResponse("Unable to find user", null));
        return;
    }

    if (array_key_exists("redirect_url", $request_data["account_details"])) {
        $user->redirect_url = $request_data["account_details"]["redirect_url"];
    }

    if (!$user->insertOrUpdate()) {
        $w->out((new AxiosResponse())->setErrorResponse("Failed to update details", null));
        return;
    }

    $contact = $user->getContact();
    if (empty($contact)) {
        $w->out((new AxiosResponse())->setErrorResponse("Unable to find user", null));
        return;
    }

    $contact->fill($request_data["account_details"]);
    $contact->setTitle($request_data["account_details"]["title"]);

    if (!$contact->insertOrUpdate()) {
        $w->out((new AxiosResponse())->setErrorResponse("Failed to update details", null));
        return;
    }

    $w->out((new AxiosResponse())->setSuccessfulResponse("User details updated", null));
}
