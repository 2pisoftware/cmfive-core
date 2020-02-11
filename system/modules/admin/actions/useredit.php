<?php

/**
 * Display User edit form
 *
 * @param <type> $w
 */
function useredit_GET(Web &$w)
{
    $p = $w->pathMatch("id", "box");
    $user = $w->Auth->getObject("User", $p["id"]);
    $w->ctx('availableLocales', $w->getAvailableLanguages());

    if ($user) {
        $w->Admin->navigation($w, "Administration - Edit User - " . $user->login);
    } else {
        if (!$p['box']) {
            $w->error("User " . $w->ctx("id") . " does not exist.", "/admin/users");
        }
    }
    $w->ctx("user", $user);

    // no layout if displayed in a box
    if ($p['box']) {
        $w->setLayout(null);
    }

    $contact = $user->getContact();

    $groups = [];
    $groupUsers = $user->isInGroups();

    if (!empty($groupUsers)) {
        foreach ($groupUsers as $groupUser) {
            $group = $groupUser->getGroup();

            $groups[] = " - " . Html::a("/admin/moreInfo/" . $group->id, $group->login);
        }
    }

    $form_data = [
        "General" => [
            [
                ["Login", "text", "login", $user->login],
                ["Admin", "checkbox", "is_admin", $user->is_admin],
                ["Active", "checkbox", "is_active", $user->is_active],
                ["External", "checkbox", "is_external", $user->is_external],
                ["Language", "select", "language", $user->language],
            ],
        ],
        "Security" => [
            [

                ["Password", "password", "password"],
                ["Repeat Password", "password", "password2"],
                ["MFA Required", "checkbox", "is_mfa_required", $user->is_mfa_required],
            ],
        ],
        "Personal" => [
            [
                ["First Name", "text", "firstname", $contact ? $contact->firstname : ""],
                ["Last Name", "text", "lastname", $contact ? $contact->lastname : ""],
                ["Title", "autocomplete", "title", $contact->getTitle(), $w->Lookup->getLookupByType("title")],
                ["Email", "text", "email", $contact ? $contact->email : ""],
            ],
        ],
        "Groups" => [
            [
                ["Group Title", "static", "groupName", implode("<br/>", $groups)],
            ],
        ],
    ];

    $w->ctx("form", Html::multiColForm($form_data, $w->localUrl("/admin/useredit/" . $w->ctx("id")), "POST", "Save", null, null, null, "_self", true, User::$_validation));
}

/**
 * Handle User Edit form submission
 *
 * @param <type> $w
 */
function useredit_POST(Web &$w)
{
    $w->pathMatch("id");

    $errors = $w->validate([
        ["login", ".+", "Login is mandatory"],
    ]);

    if ($_REQUEST['password'] && ($_REQUEST['password'] != $_REQUEST['password2'])) {
        $error[] = "Passwords don't match";
    }

    $user = $w->Auth->getObject("User", $w->ctx('id'));
    if (!$user) {
        $errors[] = "User does not exist";
    }

    if (sizeof($errors) != 0) {
        $w->error(implode("<br/>\n", $errors), "/admin/useredit/" . $w->ctx("id"));
    }

    $user->login = $_REQUEST['login'];
    $user->fill($_REQUEST);

    isset($_REQUEST['password']) ? $user->setPassword($_REQUEST['password']) : $user->password = null;
    $user->is_admin = isset($_REQUEST['is_admin']) ? 1 : 0;
    $user->is_active = isset($_REQUEST['is_active']) ? 1 : 0;
    $user->is_external = isset($_REQUEST['is_external']) ? 1 : 0;
    $user->is_mfa_required = isset($_REQUEST['is_mfa_required']) ? 1 : 0;
    $user->update();

    $contact = $user->getContact();
    if ($contact) {
        $contact->fill($_REQUEST);
        $contact->private_to_user_id = null;
        $contact->setTitle($_REQUEST['acp_title']);
        $contact->update(true); // we want to insert null values
    }

    $w->callHook("admin", "account_changed", $user);

    $w->msg("<div id='saved_record_id' data-id='" . $user->id . "' >User " . $user->login . " updated.</div>", "/admin/users");
}
