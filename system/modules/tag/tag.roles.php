<?php
/**
 * tag user roles 
 *
 * @author Robert Lockerbie, robert@lockerbie.id.au, 2015
 **/

function role_tag_admin_allowed($w, $path) {
	return startsWith($path, "tag");
}

function role_tag_user_allowed($w, $path) {
	return startsWith($path, "tag");
}
