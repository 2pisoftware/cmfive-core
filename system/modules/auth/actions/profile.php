<?php

function profile_GET(Web &$w)
{
    // $w->setTemplatePath(SYSTEM_PATH . "/templates");
    // $w->setLayout("layout");

    $user = $w->Auth->user();
    if (empty($user)) {
        $w->error("Unable to find User");
    }

    $contact = $user->getContact();
    if (empty($contact)) {
        $w->error("Unable to find User");
    }

    $user_details = [
        "id" => $user->id,
        "account" => [
            "redirect_url" => $user->redirect_url,
            "title" => $contact->getTitle(),
            "firstname" => $contact->firstname,
            "lastname" => $contact->lastname,
            "othername" => $contact->othername,
            "homephone" => $contact->homephone,
            "workphone" => $contact->workphone,
            "mobile" => $contact->mobile,
            "priv_mobile" => $contact->priv_mobile,
            "fax" => $contact->fax,
            "email" => $contact->email,
        ],
        "security" => [
            "new_password" => "",
            "repeat_new_password" => "",
            "is_mfa_enabled" => $user->is_mfa_enabled,
        ],
    ];

    $w->ctx("user", $user_details);
}
