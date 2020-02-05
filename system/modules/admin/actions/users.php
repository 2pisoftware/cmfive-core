<?php

function users_GET(Web &$w)
{
    $w->Admin->navigation($w, "Users");

    $header = ["Login", "Name", ["Admin", true], ["Active", true], ["External", true], ["Created", true], ["Last Login", true], "Operations"];
    $users = $w->Admin->getObjects("User", ["is_deleted" => 0, "is_group" => 0]);

    $internal_users = array_filter($users ?: [], function (User $user) {
        return !empty($user->id) && $user->is_external == 0;
    });

    $external_users = array_filter($users ?: [], function (User $user) {
        return !empty($user->id) && $user->is_external == 1;
    });

    $internal_data = [];
    if (!empty($internal_users)) {
        foreach ($internal_users as $internal_user) {
            $contact = $internal_user->getContact();

            $internal_data[$internal_user->id] = [
                $internal_user->login,
                $contact->getFullName(),
                [$internal_user->is_admin ? "Yes" : "No", true],
                [$internal_user->is_active ? "Yes" : "No", true],
                [$internal_user->is_external ? "Yes" : "No", true],
                [$w->Admin->time2Dt($internal_user->dt_created), true],
                [$w->Admin->time2Dt($internal_user->dt_lastlogin), true],
                Html::a("/admin/useredit/" . $internal_user->id, "Edit", null, "button tiny editbutton") .
                    Html::a("/admin/permissionedit/" . $internal_user->id, "Permissions", null, "button tiny permissionsbutton") .
                    Html::a("/admin-user/remove/" . $internal_user->id, "Remove", null, "button tiny deletebutton")
            ];
        }
    }

    $external_data = [];
    if (!empty($external_users)) {
        foreach ($external_users as $external_user) {
            $contact = $external_user->getContact();

            $external_data[$external_user->id] = [
                $external_user->login,
                !empty($contact->id) ? $contact->getFullName() : 'No Contact object found',
                [$external_user->is_admin ? "Yes" : "No", true],
                [$external_user->is_active ? "Yes" : "No", true],
                [$external_user->is_external ? "Yes" : "No", true],
                [$w->Admin->time2Dt($external_user->dt_created), true],
                [$w->Admin->time2Dt($external_user->dt_lastlogin), true],
                Html::a("/admin/useredit/" . $external_user->id, "Edit", null, "button tiny editbutton") .
                    Html::a("/admin/permissionedit/" . $external_user->id, "Permissions", null, "button tiny permissionsbutton") .
                    Html::a("/admin-user/remove/" . $external_user->id, "Remove", null, "button tiny deletebutton")
            ];
        }
    }

    $w->ctx("internal_table", Html::table($internal_data, null, "tablesorter", $header));
    $w->ctx("external_table", Html::table($external_data, null, "tablesorter", $header));
}
