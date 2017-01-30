<?php

function users_GET(Web &$w) {
	$w->Admin->navigation($w, "Users");

	$header = [__("Login"), __("First Name"), __("Last Name"), [__("Admin"), true], [__("Active"), true], [__("Created"), true], [__("Last Login"), true], __("Operations")];
	$users = $w->Admin->getObjects("User", ["is_deleted" => 0, "is_group" => 0]);
	$data = [];
	foreach ($users as $user) {
            $contact = $user->getContact();
            
            $data[$user->id] = [
                $user->login, 
				!empty($contact->firstname) ? $contact->firstname : '', 
				!empty($contact->lastname) ? $contact->lastname : '',
                [$user->is_admin ? __("Yes") : __("No"), true],
                [$user->is_active ? __("Yes") : __("No"), true],
                [$w->Admin->time2Dt($user->dt_created), true],
                [$w->Admin->time2Dt($user->dt_lastlogin), true],
                Html::a("/admin/useredit/".$user->id, __("Edit"), null, "button tiny editbutton") .
				Html::a("/admin/permissionedit/".$user->id, __("Permissions"), null, "button tiny permissionsbutton") .
                Html::a("/admin-user/remove/".$user->id, __("Remove"), null, "button tiny deletebutton")
            ];
	}
	$w->ctx("table", Html::table($data, null, "tablesorter", $header));
}
