<?php

function profile_GET(Web &$w)
{
    CmfiveScriptComponentRegister::registerComponent(
        "UserEditComponent",
        new CmfiveScriptComponent(
            "/system/templates/base/dist/UserSecurity.js",
            ["weight" => "200", "type" => "module"]
        )
    );

    CmfiveScriptComponentRegister::registerComponent(
        "AuthProfileForm",
        new CmfiveScriptComponent(
            "/system/templates/base/dist/AuthProfileForm.js",
            ["weight" => "200", "type" => "module"]
        )
    );

    $user = AuthService::getInstance($w)->user();
    if (empty($user)) {
        $w->error("Unable to find User");
    }

    $contact = $user->getContact();
    if (empty($contact)) {
        $w->error("Unable to find User");
    }

    $titles_array = [];

    foreach (AuthService::getInstance($w)->getTitles() as $title) {
        $titles_array[] = [
            "id" => $title->id,
            "name" => $title->title,
        ];
    }

    $user_details = [
        "id" => $user->id,
        "account" => [
            "redirect_url" => $user->redirect_url,
            "title" => $contact->getTitle(),
            "titles" => $titles_array,
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
