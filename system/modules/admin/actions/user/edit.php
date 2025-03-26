<?php

use Html\Form\Select;
use Html\Cmfive\SelectWithOther;

function edit_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');

    VueComponentRegister::registerComponent("autocomplete", new VueComponent("autocomplete", "/system/templates/vue-components/form/elements/autocomplete.vue.js", "/system/templates/vue-components/form/elements/autocomplete.vue.css"));
    // CmfiveScriptComponentRegister::registerComponent("toast", new CmfiveScriptComponent("/system/templates/base/dist/Toast.js"));

    CmfiveScriptComponentRegister::registerComponent(
        "UserEditComponent",
        new CmfiveScriptComponent(
            "/system/templates/base/dist/UserSecurity.js",
            ["weight" => "200", "type" => "module"]
        )
    );

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
            "is_admin" => $user->is_admin ? 'true' : 'false',
            "is_active" => $user->is_active ? 'true' : 'false',
            "is_external" => $user->is_external ? 'true' : 'false',
            "is_locked" => $user->is_locked,
            "new_password" => "",
            "repeat_new_password" => "",
            "is_mfa_enabled" => $user->is_mfa_enabled ? 'true' : 'false',
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
                    'value' => StringSanitiser::sanitise($user->login),
                ])),
                (new \Html\Form\InputField\Checkbox([
                    "id|name" => "admin",
                    'label' => 'Admin',
                    "class" => "",
                ]))->setChecked($user->is_admin),
                (new \Html\Form\InputField\Checkbox([
                    "id|name" => "active",
                    'label' => 'Active',
                    "class" => "",
                ]))->setChecked($user->is_active),
                (new \Html\Form\InputField\Checkbox([
                    "id|name" => "external",
                    'label' => 'External',
                    "class" => "",
                ]))->setChecked($user->is_external),
                (new Select([
                    "id|name" => "language",
                    'label' => 'Language',
                    'selected_option' => StringSanitiser::sanitise($user->language),
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
                    "value" => StringSanitiser::sanitise($user_details["account"]["firstname"]),
                ])),
                (new \Html\Form\InputField([
                    "id|name" => "lastname",
                    'label' => 'Last Name',
                    'required' => true,
                    "value" => StringSanitiser::sanitise($user_details["account"]["lastname"]),
                ])),
            ],
            [
                (new SelectWithOther([
                    "id|name" => "title_lookup_id",
                    'label' => 'Title',
                    'selected_option' => !empty($contact->title_lookup_id) ? LookupService::getInstance($w)->getLookup($contact->title_lookup_id)->code : null,
                    'options' => array_map(function(Lookup $lookup) use ($w) {
                        $lookup->title = StringSanitiser::sanitise($lookup->title);
                        $lookup->code = StringSanitiser::sanitise($lookup->code);
                        return $lookup;
                    }, LookupService::getInstance($w)->getLookupByType("title")),
                    'other_field' => new \Html\Form\InputField([
                        'id|name' => 'title_other',
                        'placeholder' => 'Other Title'
                    ]),
                ])),
                (new \Html\Form\InputField([
                    "id|name" => "othername",
                    'label' => 'Other Name',
                    "value" => StringSanitiser::sanitise($user_details["account"]["othername"]),
                ]))
            ],
            [
                (new \Html\Form\InputField\Tel([
                    "id|name" => "homephone",
                    'label' => 'Home Phone',
                    "value" => StringSanitiser::sanitise($user_details["account"]["homephone"]),
                ])),
                (new \Html\Form\InputField\Tel([
                    "id|name" => "workphone",
                    'label' => 'Work Phone',
                    "value" => StringSanitiser::sanitise($user_details["account"]["workphone"]),
                ])),
                (new \Html\Form\InputField\Tel([
                    "id|name" => "mobile",
                    'label' => 'Mobile',
                    "value" => StringSanitiser::sanitise($user_details["account"]["mobile"]),
                ])),
            ],
            [
                (new \Html\Form\InputField\Tel([
                    "id|name" => "priv_mobile",
                    'label' => 'Private Mobile',
                    "value" => StringSanitiser::sanitise($user_details["account"]["priv_mobile"]),
                ])),
                (new \Html\Form\InputField([
                    "id|name" => "fax",
                    'label' => 'Fax',
                    "value" => StringSanitiser::sanitise($user_details["account"]["fax"]),
                ])),
                (new \Html\Form\InputField\Email([
                    "id|name" => "email",
                    'label' => 'Email',
                    "value" => StringSanitiser::sanitise($user_details["account"]["email"]),
                ])),
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

    if (!empty($_POST["admin"])) {
        if ($_POST["admin"] === "on" || $_POST["admin"] === "off") {
            $user->is_admin = $_POST["admin"] === "on";
        } else {
            $user->is_admin = $_POST["admin"];  // backwards compat
        }
    } else {
        $user->is_admin = 0;
    }

    if (!empty($_POST["active"])) {
        if ($_POST["active"] === "on" || $_POST["active"] === "off") {
            $user->is_active = $_POST["active"] === "on";
        } else {
            $user->is_active = $_POST["active"];  // backwards compat
        }
    } else {
        $user->is_active = 0;
    }

    if (!empty($_POST["external"])) {
        if ($_POST["external"] === "on" || $_POST["external"] === "off") {
            $user->is_external = $_POST["external"] === "on";
        } else {
            $user->is_external = $_POST["external"];  // backwards compat
        }
    } else {
        $user->is_external = 0;
    }

    if (!$user->insertOrUpdate()) {
        $w->error("Failed to update User details", $redirect_url);
    }

    $contact = $user->getContact();
    if (empty($contact)) {
        $w->error("Unable to find user", $redirect_url);
    }

    $contact->fill($_POST);
    if ($_POST['title_lookup_id'] === "other") {
        $contact->setTitle($_POST['title_other']);
    } else {
        $contact->setTitle($_POST['title_lookup_id']);
    }

    if (!$contact->insertOrUpdate(true)) {  // need true to be able to update the title if no title selected now when one had been selected prior
        $w->error("Failed to update contact details", $redirect_url);
    }

    $w->msg("User details updated", $redirect_url);
}
