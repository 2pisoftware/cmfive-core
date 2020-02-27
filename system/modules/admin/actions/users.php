<?php

function users_GET(Web &$w)
{
    $w->Admin->navigation($w, "Users");
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
                $contact->firstname,
                $contact->lastname,
                [$internal_user->is_admin ? "Yes" : "No", true],
                [$internal_user->is_active ? "Yes" : "No", true],
                [$internal_user->is_mfa_enabled ? "Yes" : "No", true],
                [$internal_user->is_mfa_required ? "Yes" : "No", true],
                [$w->Admin->time2Dt($internal_user->dt_created), true],
                [empty($internal_user->dt_lastlogin) ? "" : $w->Admin->time2Dt($internal_user->dt_lastlogin), true],
                Html::b("/admin/useredit/" . $internal_user->id, "Old Edit", null, "button tiny editbutton") .
                Html::b("/admin-user/edit/" . $internal_user->id, "Edit", null, "button tiny") .
                Html::b("/admin/permissionedit/" . $internal_user->id, "Permissions", null, "button tiny permissionsbutton") .
                Html::b("/admin-user/remove/" . $internal_user->id, "Remove", null, "button tiny deletebutton", false, "alert")
            ];
        }
    }

    $external_data = [];
    if (!empty($external_users)) {
        foreach ($external_users as $external_user) {
            $contact = $external_user->getContact();

            $external_data[$external_user->id] = [
                $external_user->login,
                !empty($contact->id) ? $contact->firstname : 'No Contact object found',
                !empty($contact->id) ? $contact->lastname : 'No Contact object found',
                [$external_user->is_admin ? "Yes" : "No", true],
                [$external_user->is_active ? "Yes" : "No", true],
                [$w->Admin->time2Dt($external_user->dt_created), true],
                [empty($internal_user->dt_lastlogin) ? "" : $w->Admin->time2Dt($internal_user->dt_lastlogin), true],
                Html::b("/admin/useredit/" . $external_user->id, "Edit", null, "button tiny editbutton") .
                Html::b("/admin/permissionedit/" . $external_user->id, "Permissions", null, "button tiny permissionsbutton") .
                Html::b("/admin-user/remove/" . $external_user->id, "Remove", null, "button tiny deletebutton", false, "alert")
            ];
        }
    }

    $internal_header = ["Login", "First Name", "Last Name", ["Admin", true], ["Active", true], ["Is MFA Enabled", true], ["Is MFA Required", true], ["Created", true], ["Last Login", true], "Operations"];
    $external_header = ["Login", "First Name", "Last Name", ["Admin", true], ["Active", true], ["Created", true], ["Last Login", true], "Operations"];

    $w->ctx("internal_table", Html::table($internal_data, null, "tablesorter", $internal_header));
    $w->ctx("external_table", Html::table($external_data, null, "tablesorter", $external_header));
}
