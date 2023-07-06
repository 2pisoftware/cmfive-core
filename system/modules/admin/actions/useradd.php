<?php

use Html\Form\InputField\Password;
use Html\Form\Select;

/**
 * Display User edit form in colorbox
 *
 * @param <type> $w
 */
function useradd_GET(Web $w)
{
    $p = $w->pathMatch("box");
    //$w->setLayout("layout-2021");
    $w->setLayout('layout-bootstrap-5');

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
        (new \Html\Form\InputField([
            "id|name" => "login",
            'label' => 'Login',
            "required" => true,
        ])),
        (new \Html\Form\InputField\Checkbox([
            "id|name" => "is_admin",
            'label' => 'Admin',
            "class" => "",
        ])),
        (new \Html\Form\InputField\Checkbox([
            "id|name" => "is_active",
            'label' => 'Active',
            "class" => "",
        ])),
        (new \Html\Form\InputField\Checkbox([
            "id|name" => "is_external",
            'label' => 'External',
            "class" => "",
        ])),
        (new Select([
            "id|name" => "language",
            'label' => 'Language',
            'options' => $availableLocales,
        ])),
    ];
    
    $form['User Details'][] = [
        $password_field,
        $password_confirm_field,
    ];
    
    $form['Contact Details'][] = [
        (new \Html\Form\InputField([
            "id|name" => "firstname",
            'label' => 'First Name',
            "required" => true,
        ])),
        (new \Html\Form\InputField([
            "id|name" => "lastname",
            'label' => 'Last Name',
            "required" => true,
        ])),
    ];

    $form['Contact Details'][] = [
        (new Select([
            "id|name" => "acp_title",
            'label' => 'Title',
            'options' => LookupService::getInstance($w)->getLookupByType("title"),
        ])),
        (new \Html\Form\InputField\Email([
            "id|name" => "email",
            'label' => 'Email',
        ]))
    ];

    $form['User Roles'][] = [];  // Add heading for User Permissions

    // Display permissions grouped by module
    $allroles = AuthService::getInstance($w)->getAllRoles();

    foreach ($allroles as $role) {
        $parts = explode("_", $role);

        if (count($parts) == 1) {
            array_unshift($parts, "admin");
        }

        $module = array_shift($parts);

        $result[$module][] = implode("_", $parts);
    }

    $permission = array();

    foreach ($result as $module => $parts) {
        $parts = array_chunk($parts, 4);

        foreach ($parts as $level => $roles) {
            foreach ($roles as $r) {
                $roleName = $module == "admin" ? $r : implode("_", array($module, $r));
                $permission[ucwords($module)][$level][] = (new \Html\Form\InputField\Checkbox([
                    "id|name" => "check_" . $roleName,
                    'label' => $roleName,
                    "class" => "checkbox"
                ])); //array($roleName, "checkbox", "check_" . $roleName, null);
            }
        }
    }

    $w->out(HtmlBootstrap5::multiColForm(array_merge($form, $permission), $w->localUrl("/admin/useradd"), "POST", "Save", null, null, null, "_self", true, array_merge(User::$_validation, ['password' => ['required'], 'password2' => ['required']])));
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
            $user->addRole($r);
        }
    }
    $w->callHook("admin", "account_changed", $user);

    $w->msg("<div id='saved_record_id' data-id='" . $user->id . "' >User " . $user->login . " added</div>", "/admin/users");
}
