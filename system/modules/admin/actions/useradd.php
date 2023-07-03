<?php

use Html\Form\InputField\Password;

/**
 * Display User edit form in colorbox
 *
 * @param <type> $w
 */
function useradd_GET(Web $w)
{
    $p = $w->pathMatch("box");
    $w->setLayout("layout-2021");

    $availableLocales = $w->getAvailableLanguages();

    if (!$p['box']) {
        AdminService::getInstance($w)->navigation($w, "Add User");
    } else {
        $w->setLayout(null);
    }

    $password_field = (new Password([
        'id|name' => 'password',
        'label' => 'Password'
    ]));

    $password_confirm_field = (new Password([
        'id|name' => 'password2',
        'label' => 'Repeat password'
    ]));

    if (Config::get('auth.login.password.enforce_length') === true) {
        $password_field->setMinlength(Config::get('auth.login.password.min_length', 8));
        $password_confirm_field->setMinlength(Config::get('auth.login.password.min_length', 8));
    }

    $form['User Details'][] = [
        ["Login","text","login"],
        ["Admin","checkbox","is_admin"],
        ["Active","checkbox","is_active"],
        ["External", "checkbox", "is_external"],
        ["Language", "select", "language", null, $availableLocales],
    ];
    
    $form['User Details'][] = [
        $password_field,
        $password_confirm_field,
    ];
    
    $form['Contact Details'][] = [
        ["First Name", "text", "firstname"],
        ["Last Name", "text", "lastname"],
    ];

    $form['Contact Details'][] = [
        ["Title", "autocomplete", "title", null, LookupService::getInstance($w)->getLookupByType("title")],
        ["Email", "text", "email"],
    ];
    
    $roles = AuthService::getInstance($w)->getAllRoles();
    $roles = array_chunk($roles, 4);
    foreach ($roles as $r) {
        $row = [];
        foreach ($r as $rf) {
            $row[] = [$rf, "checkbox", "check_" . $rf];
        }
        $form['User Roles'][] = $row;
    }

    $w->out(HtmlBootstrap5::multiColForm($form, $w->localUrl("/admin/useradd"), "POST", "Save", null, null, null, "_self", true, array_merge(User::$_validation, ['password' => ['required'], 'password2' => ['required']])));
}

/**
 * Handle User Edit form submission
 *
 * @param <type> $w
 */
function useradd_POST(Web &$w)
{
    $errors = $w->validate([
        ["login", ".+", "Login is mandatory"],
        ["password", ".+", "Password is mandatory"],
        ["password2", ".+", "Password confirm is mandatory"],
    ]);
    if ($_REQUEST['password2'] != $_REQUEST['password']) {
        $errors[] = "Passwords don't match";
    }
    if (sizeof($errors) != 0) {
        $w->error(implode("<br/>\n", $errors), "/admin/useradd");
    }

    // first saving basic contact info
    $contact = new Contact($w);
    $contact->fill($_REQUEST);
    $contact->dt_created = time();
    $contact->private_to_user_id = null;
    $contact->setTitle($_REQUEST['acp_title']);
    $contact->insert();

    // now saving the user
    $user = new User($w);
    $user->login = $_REQUEST['login'];
    $user->language = $_REQUEST['language'];
    $user->is_active = !empty($_REQUEST['is_active']) ? $_REQUEST['is_active'] : 0;
    $user->is_admin = !empty($_REQUEST['is_admin']) ? $_REQUEST['is_admin'] : 0;
    $user->is_group = 0;
    $user->is_external = isset($_REQUEST['is_external']) ? 1 : 0;
    $user->dt_created = time();
    $user->contact_id = $contact->id;
    $user->setPassword($_REQUEST['password'], false);
    $user->insert();
    $w->ctx("user", $user);

    // now saving the roles
    $roles = AuthService::getInstance($w)->getAllRoles();
    foreach ($roles as $r) {
        if (!empty($_REQUEST["check_" . $r])) {
            if ($_REQUEST["check_" . $r] == 1) {
                $user->addRole($r);
            }
        }
    }
    $w->callHook("admin", "account_changed", $user);

    $w->msg("<div id='saved_record_id' data-id='" . $user->id . "' >User " . $user->login . " added</div>", "/admin/users");
}
