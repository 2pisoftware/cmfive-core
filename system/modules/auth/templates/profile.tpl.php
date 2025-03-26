<?php

use Html\Form\Html5Autocomplete;
use Html\Form\InputField;

$w->setTemplatePath(SYSTEM_PATH . "/templates");
$w->setLayout("layout")
?>

<h3>Edit Profile</h3>

<div class="tabs">
    <div class="tab-head">
        <a href="#details">Details</a>
        <a href="#user_security_app">Security</a>
    </div>

    <div class="tab-body">
        <div id="details">
            <?php
            // see: assets/ts/AuthProfileForm.ts for submit handler
            echo HtmlBootstrap5::multiColForm([
                "Application" => [
                    [new InputField\Hidden([
                        "id|name" => "user_id",
                        "value" => $user["id"],
                    ])],

                    [
                        new InputField([
                            "label" => "Redirect URL",
                            "value" => $user["account"]["redirect_url"],
                            "id|name" => "redirect_url",
                        ])
                    ]
                ],
                "Personal" => [
                    [
                        new InputField([
                            "label" => "First Name",
                            "id|name" => "firstname",
                            "value" => $user["account"]["firstname"],
                        ]),
                        new InputField([
                            "label" => "Last Name",
                            "id|name" => "lastname",
                            "value" => $user["account"]["lastname"],
                        ])
                    ],
                    [
                        new Html5Autocomplete([
                            "id|name" => "title",
                            "class" => "form-control",
                            "label" => "Title",
                            "maxItems" => 1,
                            "options" => $user["account"]["titles"],
                            "value" => $user["account"]["title"]
                        ]),
                        new InputField([
                            "label" => "Other Name",
                            "id|name" => "othername",
                            "value" => $user["account"]["othername"],
                        ])
                    ],
                    [
                        new Html5Autocomplete([
                            "id|name" => "language",
                            "class" => "form-control",
                            "label" => "Language",
                            "maxItems" => 1,
                            "options" => array_map(function ($lang) {
                                $lang->name = $lang->name;
                                $lang->native_name = $lang->native_name;
                                $lang->iso_639_1 = $lang->iso_639_1;
                                $lang->iso_639_2 = $lang->iso_639_2;
                                return $lang;
                            }, AdminService::getInstance($w)->getLanguages()),
                            "value" => $user["account"]["language"],
                        ])
                    ]
                ],
                "Contact" => [
                    [
                        new InputField\Tel([
                            "label" => "Home Phone",
                            "id|name" => "homephone",
                            "value" => $user["account"]["homephone"],
                        ]),

                        new InputField\Tel([
                            "label" => "Work Phone",
                            "id|name" => "workphone",
                            "value" => $user["account"]["workphone"],
                        ]),

                        new InputField\Tel([
                            "label" => "Mobile",
                            "id|name" => "mobile",
                            "value" => $user["account"]["mobile"],
                        ]),
                    ],

                    [
                        new InputField\Tel([
                            "label" => "Private Mobile",
                            "id|name" => "priv_mobile",
                            "value" => $user["account"]["priv_mobile"],
                        ]),

                        new InputField\Tel([
                            "label" => "Fax",
                            "id|name" => "fax",
                            "value" => $user["account"]["fax"],
                        ]),

                        new InputField\Email([
                            "label" => "Email",
                            "id|name" => "email",
                            "value" => $user["account"]["email"]
                        ])
                    ]
                ]
            ], null, "POST", "Update", "user_details_form", null, null, "_self", true, null, false);
            ?>
        </div>

        <div id="user_security_app">
            <user-security-component
                user_id="<?php echo $user["id"]; ?>"
                locked="false"
                mfa_enabled="<?php echo $user["security"]["is_mfa_enabled"]; ?>"
                pw_min_length="<?php echo Config::get('auth.login.password.min_length', 8); ?>">
            </user-security-component>
        </div>
    </div>
</div>