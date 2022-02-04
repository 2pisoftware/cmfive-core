<?php

function edit_GET(Web $w)
{
    VueComponentRegister::registerComponent("autocomplete", new VueComponent("autocomplete", "/system/templates/vue-components/form/elements/autocomplete.vue.js", "/system/templates/vue-components/form/elements/autocomplete.vue.css"));

    $redirect_url = "/admin/users";

    list($user_id) = $w->pathMatch("id");
    if (empty($user_id)) {
        $w->error("Unable to find User", $redirect_url);
    }

    $user = AuthService::getInstance($w)->getUser($user_id);
    if (empty($user)) {
        $w->error("Unable to find User", $redirect_url);
    }

    $contact = $user->getContact();
    if (empty($contact)) {
        $w->error("Unable to find User", $redirect_url);
    }

    $groups = [];
    $group_users = $user->isInGroups();

    if (!empty($group_users)) {
        foreach ($group_users as $group_user) {
            $group = $group_user->getGroup();
            $groups[] = [
                "url" =>  WEBROOT . "/admin/moreInfo/$group->id",
                "title" => $group->login
            ];
        }
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
            "login" => $user->login,
            "is_admin" => $user->is_admin,
            "is_active" => $user->is_active,
            "is_external" => $user->is_external,
            "new_password" => "",
            "repeat_new_password" => "",
            "is_mfa_enabled" => $user->is_mfa_enabled,
        ],
        "groups" => $groups,
    ];

    $w->ctx("user", $user_details);
}
