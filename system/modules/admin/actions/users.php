<?php

function users_GET(Web &$w) {
	$w->Admin->navigation($w, "Users");

	$header = ["Login", "First Name", "Last Name", ["Admin", true], ["Active", true], ["Created", true], ["Last Login", true], "Operations"];
	$users = $w->Admin->getObjects("User", ["is_deleted" => 0, "is_group" => 0]);
	$data = [];
	foreach ($users as $user) {
            $contact = $user->getContact();
            
            $data[$user->id] = [
                $user->login, 
				!empty($contact->firstname) ? $contact->firstname : '', 
				!empty($contact->lastname) ? $contact->lastname : '',
                [$user->is_admin ? "Yes" : "No", true],
                [$user->is_active ? "Yes" : "No", true],
                [$w->Admin->time2Dt($user->dt_created), true],
                [$w->Admin->time2Dt($user->dt_lastlogin), true],
                Html::a("/admin/useredit/".$user->id, "Edit", null, "button tiny editbutton") .
				Html::a("/admin/permissionedit/".$user->id, "Permissions", null, "button tiny permissionsbutton") .
                Html::a("/admin-user/remove/".$user->id, "Remove", null, "button tiny deletebutton")
            ];
	}
	$w->ctx("table", Html::table($data, null, "tablesorter", $header));
}
