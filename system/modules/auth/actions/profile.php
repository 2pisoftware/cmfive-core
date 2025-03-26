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
            "title" => StringSanitiser::sanitise($contact->getTitle()),
            "titles" => $titles_array,
            "firstname" => StringSanitiser::sanitise($contact->firstname),
            "lastname" => StringSanitiser::sanitise($contact->lastname),
            "othername" => StringSanitiser::sanitise($contact->othername),
            "homephone" => StringSanitiser::sanitise($contact->homephone),
            "workphone" => StringSanitiser::sanitise($contact->workphone),
            "mobile" => StringSanitiser::sanitise($contact->mobile),
            "priv_mobile" => StringSanitiser::sanitise($contact->priv_mobile),
            "fax" => StringSanitiser::sanitise($contact->fax),
            "email" => StringSanitiser::sanitise($contact->email),
            "language" => StringSanitiser::sanitise($user->language),
        ],
        "security" => [
            "new_password" => "",
            "repeat_new_password" => "",
            "is_mfa_enabled" => $user->is_mfa_enabled,
        ],
    ];
// echo "<pre>";
//     var_dump($user_details);
// echo "</pre>";
    $w->ctx("user", $user_details);
}
