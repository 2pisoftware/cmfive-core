<?php
/**
* Display User edit form in colorbox
*
* @param <type> $w
*/
function useradd_GET(Web &$w) {
	$p = $w->pathMatch("box");
	if (!$p['box']) {
		$w->Admin->navigation($w,"Add User");
	} else {
		$w->setLayout(null);
	}
}

/**
 * Handle User Edit form submission
 *
 * @param <type> $w
 */
function useradd_POST(Web &$w) {
	$errors = $w->validate(array(
	array("login",".+","Login is mandatory"),
	array("password",".+","Password is mandatory"),
	array("password2",".+","Password2 is mandatory"),
	));
	if ($_REQUEST['password2'] != $_REQUEST['password']) {
		$errors[]="Passwords don't match";
	}
	if (sizeof($errors) != 0) {
		$w->error(implode("<br/>\n",$errors),"/admin/useradd");
	}

	// first saving basic contact info
	$contact = new Contact($w);
	$contact->fill($_REQUEST);
	$contact->dt_created = time();
	$contact->private_to_user_id= null;
	$contact->insert();

	// now saving the user
	$user = new User($w);
	$user->login = $_REQUEST['login'];
	
	$user->is_admin = isset($_REQUEST['is_admin']) ? 1 : 0;
    $user->is_active = isset($_REQUEST['is_active']) ? 1 : 0;
    $user->is_external = isset($_REQUEST['is_external']) ? 1 : 0;
    $user->is_group = 0;
	$user->dt_created = time();
	$user->contact_id = $contact->id;
	$user->insert();
	$user->setPassword($_REQUEST['password']);
	$user->update();
	$w->ctx("user", $user);

	// now saving the roles
	$roles = $w->Auth->getAllRoles();
	foreach ($roles as $r) {
		if (!empty($_REQUEST["check_".$r])){
			if ($_REQUEST["check_".$r]==1) {
				$user->addRole($r);
			}
		}
	}
	$w->callHook("admin", "account_changed", $user);

	$w->msg("<div id='saved_record_id' data-id='".$user->id."' >User ".$user->login." added</div>","/admin/users");
}
