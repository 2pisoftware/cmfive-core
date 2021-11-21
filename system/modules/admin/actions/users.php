<?php

function users_GET(Web $w)
{
    $w->setLayout("layout-bootstrap-5");
    AdminService::getInstance($w)->navigation($w, "Users");

    $internal_page_number = $w->sessionOrRequest('cmfive-internal-users__page-number', 1);
    $internal_page_size = $w->sessionOrRequest('cmfive-internal-users__page-size', 50);
    $internal_sort = $w->sessionOrRequest('cmfive-internal-users__sort', 'login');
    $internal_sort_direction = $w->sessionOrRequest('cmfive-internal-users__sort-direction', 'asc');

    $external_page_number = $w->sessionOrRequest('cmfive-internal-users__page-number', 1);
    $external_page_size = $w->sessionOrRequest('cmfive-external-users__page-size', 50);
    $external_sort = $w->sessionOrRequest('cmfive-external-users__sort', 'login');
    $external_sort_direction = $w->sessionOrRequest('cmfive-external-users__sort-direction', 'asc');

    $internal_users = AdminService::getInstance($w)->getUsers(["user.is_external" => 0, "user.is_deleted" => 0, "user.is_group" => 0], $internal_page_number, $internal_page_size, $internal_sort, $internal_sort_direction);
    $internal_user_count = AdminService::getInstance($w)->countUsers(["is_external" => 0, "is_deleted" => 0, "is_group" => 0]);

    $external_users = AdminService::getInstance($w)->getUsers(["user.is_external" => 1, "user.is_deleted" => 0, "user.is_group" => 0], $external_page_number, $external_page_size, $external_sort, $external_sort_direction);
    $external_user_count = AdminService::getInstance($w)->countUsers(["is_external" => 1, "is_deleted" => 0, "is_group" => 0]);

    $internal_data = [];
    if (!empty($internal_users)) {
        foreach ($internal_users as $internal_user) {
            $contact = $internal_user->getContact();

            $internal_data[] = [
                ($internal_user->is_locked ? '<i class="bi bi-lock"></i>' : '') . $internal_user->login,
                !empty($contact) ? $contact->firstname . ' ' . $contact->lastname : "",
                // !empty($contact) ? $contact->lastname : "",
                $internal_user->is_admin ? "Yes" : "No",
                $internal_user->is_active ? "Yes" : "No",
                $internal_user->is_mfa_enabled ? "Yes" : "No",
                // AdminService::getInstance($w)->time2Dt($internal_user->dt_created),
                empty($internal_user->dt_lastlogin) ? "" : AdminService::getInstance($w)->time2Dt($internal_user->dt_lastlogin),
                HtmlBootstrap5::buttonGroup(
                    HtmlBootstrap5::b("/admin-user/edit/" . $internal_user->id, "Edit", null, "editbutton", false, 'btn-sm btn-secondary') .
                    HtmlBootstrap5::b("/admin/permissionedit/" . $internal_user->id, "Permissions", null, "permissionsbutton", false, 'btn-sm btn-info') .
                    HtmlBootstrap5::b("/admin-user/remove/" . $internal_user->id, "Remove", null, "deletebutton", false, "btn-sm btn-danger")
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
                // !empty($contact->id) ? $contact->lastname : 'No Contact object found',
                // [$external_user->is_admin ? "Yes" : "No", true],
                // [$external_user->is_active ? "Yes" : "No", true],
                AdminService::getInstance($w)->time2Dt($external_user->dt_created),
                // [empty($internal_user->dt_lastlogin) ? "" : AdminService::getInstance($w)->time2Dt($internal_user->dt_lastlogin), true],
                HtmlBootstrap5::buttonGroup(
                    HtmlBootstrap5::b("/admin-user/edit/" . $external_user->id, "Edit", null, "editbutton", false, 'btn-sm btn-secondary') .
                    HtmlBootstrap5::b("/admin/permissionedit/" . $external_user->id, "Permissions", null, "permissionsbutton", false, 'btn-sm btn-info') .
                    HtmlBootstrap5::b("/admin-user/remove/" . $external_user->id, "Remove", null, "deletebutton", false, "btn-sm btn-danger")
                )
            ];
        }
    }

    $internal_header = [['login', "Login"], ['name', "Name"], ['is_admin', "Admin"], ['is_active', "Active"], ['is_mfa_enabled', "MFA"], ['dt_last_login', "Last Login"], "Actions"];
    $external_header = [['login', "Login"], ['name', "Name"], ['dt_created', "Created"], "Actions"];

    $w->ctx("internal_table", HtmlBootstrap5::paginatedTable(
        $internal_header,
        $internal_data,
        $internal_page_number,
        $internal_page_size,
        $internal_user_count,
        "/admin/users",
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
        "/admin/users#external",
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
