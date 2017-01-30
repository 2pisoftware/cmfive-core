<?php
$form[__('User Details')][]=array(
array(__("Login"),"text","login"),
array(__("Admin"),"checkbox","is_admin"),
array(__("Active"),"checkbox","is_active"),
array(__("Language"),"select","language",$user->language,$availableLocales));

$form[__('User Details')][]=array(
array(__("Password"),"password","password"),
array(__("Repeat Password"),"password","password2"));

$form[__('Contact Details')][]=array(
array(__("First Name"),"text","firstname"),
array(__("Last Name"),"text","lastname"));
$form[__('Contact Details')][]=array(
array(__("Title"),"select","title",null,lookupForSelect($w, "title")),
array(__("Email"),"text","email"));

$roles = $w->Auth->getAllRoles();
$roles = array_chunk($roles, 4);
foreach ($roles as $r) {
	$row = array();
	foreach ($r as $rf) {
		$row[]=array($rf,"checkbox","check_".$rf);
	}
	$form['User Roles'][]=$row;
}

print Html::multiColForm($form,$w->localUrl("/admin/useradd"),"POST",__("Save"));
