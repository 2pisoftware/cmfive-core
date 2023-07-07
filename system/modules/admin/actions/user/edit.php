<?php

use Html\Form\Select;

function edit_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');

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

    $availableLocales = $w->getAvailableLanguages();

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

    $user_details = [
        "id" => $user->id,
        "account" => [
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
            "is_locked" => $user->is_locked,
            "new_password" => "",
            "repeat_new_password" => "",
            "is_mfa_enabled" => $user->is_mfa_enabled,
        ],
        "groups" => $groups,
    ];

    $w->ctx("user", $user_details);

    $w->ctx('userDetails', HtmlBootstrap5::multiColForm([
        'User Details' => [
            [
                (new \Html\Form\InputField([
                    "id|name" => "login",
                    'label' => 'Login',
                    "required" => true,
                    'value' => $user->login,
                ])),
                (new \Html\Form\InputField\Checkbox([
                    "id|name" => "admin",
                    'label' => 'Admin',
                    "class" => "",
                ]))->setAttribute("v-model", "user.security.is_admin"),
                (new \Html\Form\InputField\Checkbox([
                    "id|name" => "active",
                    'label' => 'Active',
                    "class" => "",
                ]))->setAttribute("v-model", "user.security.is_active"),
                (new \Html\Form\InputField\Checkbox([
                    "id|name" => "external",
                    'label' => 'External',
                    "class" => "",
                ]))->setAttribute("v-model", "user.security.is_external"),
                (new Select([
                    "id|name" => "language",
                    'label' => 'Language',
                    'selected_option' => $user->language,
                    'options' => $availableLocales,
                ])),
            ],
        ],
        'Contact Details' => [
            [
                (new \Html\Form\InputField([
                    "id|name" => "firstname",
                    'label' => 'First Name',
                    'required' => true,
                ]))->setAttribute("v-model", "user.account.firstname"),
                (new \Html\Form\InputField([
                    "id|name" => "lastname",
                    'label' => 'Last Name',
                    'required' => true,
                ]))->setAttribute("v-model", "user.account.lastname"),
            ],
            [
                (new Select([
                    "id|name" => "title_lookup_id",
                    'label' => 'Title',
                    'selected_option' => !empty($contact->title_lookup_id) ? LookupService::getInstance($w)->getLookup($contact->title_lookup_id)->code : null,
                    'options' => LookupService::getInstance($w)->getLookupByType("title"),
                ])),
                (new \Html\Form\InputField([
                    "id|name" => "othername",
                    'label' => 'Other Name',
                ]))->setAttribute("v-model", "user.account.othername")
            ],
            [
                (new \Html\Form\InputField\Tel([
                    "id|name" => "homephone",
                    'label' => 'Home Phone',
                ]))->setAttribute("v-model", "user.account.homephone"),
                (new \Html\Form\InputField\Tel([
                    "id|name" => "workphone",
                    'label' => 'Work Phone',
                ]))->setAttribute("v-model", "user.account.workphone"),
                (new \Html\Form\InputField\Tel([
                    "id|name" => "mobile",
                    'label' => 'Mobile',
                ]))->setAttribute("v-model", "user.account.mobile"),
            ],
            [
                (new \Html\Form\InputField\Tel([
                    "id|name" => "priv_mobile",
                    'label' => 'Private Mobile',
                ]))->setAttribute("v-model", "user.account.priv_mobile"),
                (new \Html\Form\InputField([
                    "id|name" => "fax",
                    'label' => 'Fax',
                ]))->setAttribute("v-model", "user.account.fax"),
                (new \Html\Form\InputField\Email([
                    "id|name" => "email",
                    'label' => 'Email',
                ]))->setAttribute("v-model", "user.account.email")
            ]
        ],
    ], '/admin-user/edit/' . $user->id));

}

function edit_POST(Web $w): void
{
    $redirect_url = '/admin/users/';

    list($user_id) = $w->pathMatch("id");
    if (empty($user_id)) {
        $w->error("Unable to find User", $redirect_url);
    }

    $user = AuthService::getInstance($w)->getUser($user_id);
    if (empty($user)) {
        $w->error("Unable to find User", $redirect_url);
    }

    $user->fill($_POST);
    $user->is_admin = !empty($_POST['admin']) ? $_POST['admin'] : 0;
    $user->is_active = !empty($_POST['active']) ? $_POST['active'] : 0;
    $user->is_external = isset($_POST['external']) ? 1 : 0;

    if (!$user->insertOrUpdate()) {
        $w->error("Failed to update User details", $redirect_url);
    }

    $contact = $user->getContact();
    if (empty($contact)) {
        $w->error("Unable to find user", $redirect_url);
    }

    $contact->fill($_POST);
    $contact->setTitle($_POST['title_lookup_id']);

    if (!$contact->insertOrUpdate(true)) {  // need true to be able to update the title if no title selected now when one had been selected prior
        $w->error("Failed to update contact details", $redirect_url);
    }

    $w->msg("User details updated", $redirect_url);

}