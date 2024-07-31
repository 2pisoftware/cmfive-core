<?php

function users_GET(Web $w)
{
    $w->setLayout("layout-bootstrap-5");
    AdminService::getInstance($w)->navigation($w, "Users");

    //if filter applied unset the page number
    if (array_key_exists("admin/user__filter-login", $_REQUEST) ||
        array_key_exists("admin/user__filter-name", $_REQUEST) ||
        array_key_exists("admin/user__filter-email", $_REQUEST)
    ) {
        $filter_applied = true;
        $w->sessionUnset("cmfive-internal-users__page-number");
        $w->sessionUnset("cmfive-external-users__page-number");
    } else {
        $filter_applied = false;
    }

    $internal_page_number = $w->sessionOrRequest('cmfive-internal-users__page-number', 1);
    $external_page_number = $w->sessionOrRequest('cmfive-external-users__page-number', 1);

    // Get filter parameters
    $login = $w->sessionOrRequest("admin/user__filter-login");
    $name = $w->sessionOrRequest("admin/user__filter-name");
    $email = $w->sessionOrRequest("admin/user__filter-email");
    $reset = Request::string("filter_reset_users_filter");

    $filter_url = "";

    if (!empty($reset)) {
        $login = null;
        $w->sessionUnset("admin/user__filter-login");
        $name = null;
        $w->sessionUnset("admin/user__filter-name");
        $email = null;
        $w->sessionUnset("admin/user__filter-email");
        $filter_applied = false;
    }

    if ($filter_applied === true) {
        // Get the User.ids of Internal Users that match the search criteria
        $filtered_int_user_ids = [];
        $users = AdminService::getInstance($w)->getUsers(["user.is_external" => 0, "user.is_deleted" => 0, "user.is_group" => 0]);
        if (!empty($users)) {
            $i = 0;
            foreach ($users as $user) {
                $contact = AdminService::getInstance($w)->getObject("Contact", $user->contact_id);
                if (!empty($contact) &&
                    str_contains(strtoupper($user->login), strtoupper($login)) &&
                    str_contains(strtoupper($contact->firstname . $contact->lastname), strtoupper($name)) &&
                    str_contains(strtoupper($contact->email), strtoupper($email))) {
                    $filtered_int_user_ids[$i++] = $user->id;
                }
            }
        }

        // Get the User.ids of External Users that match the search criteria
        $filtered_ext_user_ids = [];
        $users = AdminService::getInstance($w)->getUsers(["user.is_external" => 1, "user.is_deleted" => 0, "user.is_group" => 0]);
        if (!empty($users)) {
            $i = 0;
            foreach ($users as $user) {
                $contact = AdminService::getInstance($w)->getObject("Contact", $user->contact_id);
                if (!empty($contact) &&
                    str_contains(strtoupper($user->login), strtoupper($login)) &&
                    str_contains(strtoupper($contact->firstname . $contact->lastname), strtoupper($name)) &&
                    str_contains(strtoupper($contact->email), strtoupper($email))) {
                    $filtered_ext_user_ids[$i++] = $user->id;
                }
            }
        }

        // Set up the filtered part of the url
        if (!(empty($login) && empty($name) && empty($email))) {
            $filter_url = "?";
            if (!empty($login)) {
                $filter_url .= "admin%2Fuser__filter-login=" . $login;
                if (!(empty($name) && empty($email))) {
                    $filter_url .= "&";
                }
            }
            if (!empty($name)) {
                $filter_url .= "admin%2Fuser__filter-name=" . $name;
                if (!(empty($email))) {
                    $filter_url .= "&";
                }
            }
            if (!empty($email)) {
                $filter_url .= "admin%2Fuser__filter-email=" . $email;
            }
        }
    }

    // Set up base urls
    $internal_base_url = "/admin/users" . $filter_url;
    $external_base_url = $internal_base_url . "#external";

    $filterData = [
        (new \Html\Form\InputField\Text([
            'id|name' => 'admin/user__filter-login',
            'value' => $login,
            'label' => 'Login',
        ])),
        (new \Html\Form\InputField\Text([
            'id|name' => 'admin/user__filter-name',
            'value' => $name,
            'label' => 'Name',
        ])),
        (new \Html\Form\InputField\Text([
            'id|name' => 'admin/user__filter-email',
            'value' => $email,
            'label' => 'Email',
        ]))
    ];

    $internal_page_size = $w->sessionOrRequest('cmfive-internal-users__page-size', 50);
    $internal_sort = $w->sessionOrRequest('cmfive-internal-users__sort', 'login');
    $internal_sort_direction = $w->sessionOrRequest('cmfive-internal-users__sort-direction', 'asc');

    $external_page_size = $w->sessionOrRequest('cmfive-external-users__page-size', 50);
    $external_sort = $w->sessionOrRequest('cmfive-external-users__sort', 'login');
    $external_sort_direction = $w->sessionOrRequest('cmfive-external-users__sort-direction', 'asc');

    if ($filter_applied === false) {
        $internal_users = AdminService::getInstance($w)->getUsers(["user.is_external" => 0, "user.is_deleted" => 0, "user.is_group" => 0], $internal_page_number, $internal_page_size, $internal_sort, $internal_sort_direction);
        $internal_user_count = AdminService::getInstance($w)->countUsers(["is_external" => 0, "is_deleted" => 0, "is_group" => 0]);
    } elseif (!empty($filtered_int_user_ids)) {
        $internal_users = AdminService::getInstance($w)->getUsers(["user.id" => $filtered_int_user_ids], $internal_page_number, $internal_page_size, $internal_sort, $internal_sort_direction);
        $internal_user_count = AdminService::getInstance($w)->countUsers(["user.id" => $filtered_int_user_ids]);
    } else {
        $internal_users = null;
        $internal_user_count = 0;
        $internal_page_number = 1;
        $w->sessionUnset("cmfive-internal-users__page-number");
    }

    if ($filter_applied === false) {
        $external_users = AdminService::getInstance($w)->getUsers(["user.is_external" => 1, "user.is_deleted" => 0, "user.is_group" => 0], $external_page_number, $external_page_size, $external_sort, $external_sort_direction);
        $external_user_count = AdminService::getInstance($w)->countUsers(["is_external" => 1, "is_deleted" => 0, "is_group" => 0]);
    } elseif (!empty($filtered_ext_user_ids)) {
        $external_users = AdminService::getInstance($w)->getUsers(["user.id" => $filtered_ext_user_ids], $external_page_number, $external_page_size, $external_sort, $external_sort_direction);
        $external_user_count = AdminService::getInstance($w)->countUsers(["user.id" => $filtered_ext_user_ids]);
    } else {
        $external_users = null;
        $external_user_count = 0;
        $external_page_number = 1;
        $w->sessionUnset("cmfive-external-users__page-number");
    }

    $internal_data = [];
    if (!empty($internal_users)) {
        foreach ($internal_users as $internal_user) {
            $contact = $internal_user->getContact();

            $internal_data[] = [
                ($internal_user->is_locked ? '<i class="bi bi-lock"></i>' : '') . $internal_user->login,
                !empty($contact) ? $contact->firstname . ' ' . $contact->lastname : "",
                !empty($contact) ? $contact->email : "",
                $internal_user->is_admin ? "Yes" : "No",
                $internal_user->is_active ? "Yes" : "No",
                $internal_user->is_mfa_enabled ? "Yes" : "No",
                // AdminService::getInstance($w)->time2Dt($internal_user->dt_created),
                empty($internal_user->dt_lastlogin) ? "" : AdminService::getInstance($w)->time2Dt($internal_user->dt_lastlogin),
                HtmlBootstrap5::buttonGroup(
                    HtmlBootstrap5::b("/admin-user/edit/" . $internal_user->id, "Edit", null, "editbutton", false, 'btn-sm btn-secondary') .
                    HtmlBootstrap5::b("/admin/permissionedit/" . $internal_user->id, "Permissions", null, "permissionsbutton", false, 'btn-sm btn-info') .
                    HtmlBootstrap5::b("/admin-user/remove/" . $internal_user->id, "Delete", null, "deletebutton", false, "btn-sm btn-danger")
                )
            ];
        }
    }

    $external_data = [];
    if (!empty($external_users)) {
        foreach ($external_users as $external_user) {
            $contact = $external_user->getContact();

            $external_data[] = [
                $external_user->login,
                !empty($contact->id) ? $contact->firstname . ' ' . $contact->lastname : 'No Contact object found',
                !empty($contact) ? $contact->email : "",
               // !empty($contact->id) ? $contact->lastname : 'No Contact object found',
                // [$external_user->is_admin ? "Yes" : "No", true],
                // [$external_user->is_active ? "Yes" : "No", true],
                AdminService::getInstance($w)->time2Dt($external_user->dt_created),
                // [empty($internal_user->dt_lastlogin) ? "" : AdminService::getInstance($w)->time2Dt($internal_user->dt_lastlogin), true],
                HtmlBootstrap5::buttonGroup(
                    HtmlBootstrap5::b("/admin-user/edit/" . $external_user->id, "Edit", null, "editbutton", false, 'btn-sm btn-secondary') .
                    HtmlBootstrap5::b("/admin/permissionedit/" . $external_user->id, "Permissions", null, "permissionsbutton", false, 'btn-sm btn-info') .
                    HtmlBootstrap5::b("/admin-user/remove/" . $external_user->id, "Delete", null, "deletebutton", false, "btn-sm btn-danger")
                )
            ];
        }
    }

    $internal_header = [['login', "Login"], ['name', "Name"], ['email', "Email"], ['is_admin', "Admin"], ['is_active', "Active"], ['is_mfa_enabled', "MFA"], ['dt_lastlogin', "Last Login"], "Actions"];
    $external_header = [['login', "Login"], ['name', "Name"], ['email', "Email"], ['dt_created', "Created"], "Actions"];

    //send variables to template
    $w->ctx("filterData", $filterData);

    $w->ctx("internal_table", HtmlBootstrap5::paginatedTable(
        $internal_header,
        $internal_data,
        $internal_page_number,
        $internal_page_size,
        $internal_user_count,
        $internal_base_url,
        $internal_sort,
        $internal_sort_direction,
        'cmfive-internal-users__page-number',
        'cmfive-internal-users__page-size',
        'cmfive-internal-users__total-results',
        'cmfive-internal-users__sort',
        'cmfive-internal-users__sort-direction'
    ));
    // $w->ctx("internal_table", HtmlBootstrap5::table($internal_data, null, "tablesorter", $internal_header));
    $w->ctx("external_table", HtmlBootstrap5::paginatedTable(
        $external_header,
        $external_data,
        $external_page_number,
        $external_page_size,
        $external_user_count,
        $external_base_url,
        $external_sort,
        $external_sort_direction,
        'cmfive-external-users__page-number',
        'cmfive-external-users__page-size',
        'cmfive-external-users__total-results',
        'cmfive-external-users__sort',
        'cmfive-external-users__sort-direction'
    ));
    // $w->ctx("external_table", HtmlBootstrap5::table($external_data, null, "tablesorter", $external_header));
}
