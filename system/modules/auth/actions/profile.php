<?php
function profile_GET(Web &$w) {
	$p=$w->pathMatch("box");
	$user = $w->Auth->user();
	$contact = $user->getContact();
	if ($user) {
		$w->ctx("title",__("Administration - Profile - ").$user->login);
	} else {
		$w->error(__("User does not exist."));
	}

	$lines = array();

	$lines[] = array(__("Change Password"),"section");
	$lines[] = array(__("Password"),"password","password","");
	$lines[] = array(__("Repeat Password"),"password","password2","");
	$lines[] = array(__("Contact Details"),"section");
	$lines[] = array(__("First Name"),"text","firstname",$contact ? $contact->firstname : "");
	$lines[] = array(__("Last Name"),"text","lastname",$contact ? $contact->lastname : "");
	$lines[] = array(__("Communication"),"section");
	$lines[] = array(__("Home Phone"),"text","homephone",$contact ? $contact->homephone : "");
	$lines[] = array(__("Work Phone"),"text","workphone",$contact ? $contact->workphone : "");
	$lines[] = array(__("Private Mobile"),"text","priv_mobile",$contact ? $contact->priv_mobile : "");
	$lines[] = array(__("Work Mobile"),"text","mobile",$contact ? $contact->mobile : "");
	$lines[] = array(__("Fax"),"text","fax",$contact ? $contact->fax : "");
	$lines[] = array(__("Email"),"text","email",$contact ? $contact->email : "");
	$lines[] = array(__("Redirect URL"), "text", "redirect_url", $user->redirect_url);
	$lines[] = array(__("Language"), "select", "language", $user->language,$user->getAvailableLanguages());

	$f = Html::form($lines,$w->localUrl("/auth/profile"),"POST",__("Update"));
	if ($p['box']) {
		$w->setLayout(null);
		$f = "<h2>".__("Edit Profile")."</h2>".$f;
	}
	$w->out($f);
}

function profile_POST(Web &$w) {
	$w->pathMatch("id");
	
	$errors = $w->validate(array(
	array("homephone","^[0-9+\- ]*$",__("Not a valid home phone number")),
	array("workphone","^[0-9+\- ]*$",__("Not a valid work phone number")),
	array("mobile","^[0-9+\- ]*$",__("Not a valid  mobile phone number")),
	array("priv_mobile","^[0-9+\- ]*$",__("Not a valid  mobile phone number")),
	array("fax","^[0-9+\- ]*$",__("Not a valid fax number")),
	));

	if ($_REQUEST['password'] && (($_REQUEST['password'] != $_REQUEST['password2']))) {
		$errors[]=__("Passwords don't match");
	}
	$user = $w->Auth->user();

	if (!$user) {
		$errors[]=__("Not Logged In");
	}

	if (sizeof($errors) != 0) {
		$w->error(implode("<br/>\n",$errors),"/auth/profile");
	}

	$user->fill($_REQUEST);
	// Filter out everything except the path so that users cant make redirect urls out of cmfive
    $parse_url = parse_url($user->redirect_url);
    $redirect_url = $parse_url["path"];

    // Menu link doesnt like a leading slash
    if ($redirect_url[0] == "/") {
        $redirect_url = substr($redirect_url, 1);
    }
    $user->redirect_url = $redirect_url;

	if ($_REQUEST['password']) {
		$user->setPassword($_REQUEST['password']);
	} else {
		$user->password = null;
	}
	$user->update();

	$contact = $user->getContact();
	if ($contact) {
		$contact->fill($_REQUEST);
		$contact->private_to_user_id= null;
		$contact->update();
	}

	$w->msg(__("Profile updated."));
}
