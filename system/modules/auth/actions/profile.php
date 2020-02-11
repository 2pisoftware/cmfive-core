<?php

function profile_GET(Web &$w)
{
    $p = $w->pathMatch("box");
    $user = $w->Auth->user();
    $contact = $user->getContact();

    if (empty($user)) {
        $w->error("User does not exist.");
    }

    $w->ctx("title", "Administration - Profile - " . $user->login);

    $form_data = [
        "General" => [
            [
                ["Redirect URL", "text", "redirect_url", $user->redirect_url],
            ]
        ],
        "Contact" => [
            [
                ["First Name", "text", "firstname", $contact ? $contact->firstname : ""],
                ["Last Name", "text", "lastname", $contact ? $contact->lastname : ""],
            ],
            [
                ["Home Phone", "text", "homephone", $contact ? $contact->homephone : ""],
                ["Work Phone", "text", "workphone", $contact ? $contact->workphone : ""],
            ],
            [
                ["Private Mobile", "text", "priv_mobile", $contact ? $contact->priv_mobile : ""],
                ["Work Mobile", "text", "mobile", $contact ? $contact->mobile : ""],
            ],
            [
                ["Fax", "text", "fax", $contact ? $contact->fax : ""],
                ["Email", "text", "email", $contact ? $contact->email : ""],
            ]
        ],
        "Security" => [
            [
                ["Password", "password", "password", ""],
                ["Repeat Password", "password", "password2", ""],
            ],
        ],
    ];

    $form = Html::multiColForm($form_data, $w->localUrl("/auth/profile"), "POST", "Update");
    if ($p['box']) {
        $w->setLayout(null);
        $form = "<h2>Edit Profile</h2>" . $form;
    }
    $w->out($form);
}

function profile_POST(Web &$w)
{
    $w->pathMatch("id");

    $errors = $w->validate([
        ["homephone", "^[0-9+\- ]*$", "Not a valid home phone number"],
        ["workphone", "^[0-9+\- ]*$", "Not a valid work phone number"],
        ["mobile", "^[0-9+\- ]*$", "Not a valid  mobile phone number"],
        ["priv_mobile", "^[0-9+\- ]*$", "Not a valid  mobile phone number"],
        ["fax", "^[0-9+\- ]*$", "Not a valid fax number"],
    ]);

    if ($_REQUEST['password'] && (($_REQUEST['password'] != $_REQUEST['password2']))) {
        $errors[] = "Passwords don't match";
    }
    $user = $w->Auth->user();

    if (!$user) {
        $errors[] = "Not Logged In";
    }

    if (sizeof($errors) != 0) {
        $w->error(implode("<br/>\n", $errors), "/auth/profile");
    }

    $user->fill($_REQUEST);
    // Filter out everything except the path so that users cant make redirect urls out of cmfive
    $parse_url = parse_url($user->redirect_url);
    $redirect_url = $parse_url["path"];

    // Menu link doesnt like a leading slash
    if ($redirect_url[0] == "/") {
        $redirect_url = substr($redirect_url, 1);
    }
    $user->redirect_url = $redirect_url;

    if ($_REQUEST['password']) {
        $user->setPassword($_REQUEST['password']);
    } else {
        $user->password = null;
    }
    $user->update();

    $contact = $user->getContact();
    if ($contact) {
        $contact->fill($_REQUEST);
        $contact->private_to_user_id = null;
        $contact->update();
    }

    $w->msg("Profile updated.");
}
