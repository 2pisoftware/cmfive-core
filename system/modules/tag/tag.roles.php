<?php
/**
 * tag user roles 
 *
 * @author Robert Lockerbie, robert@lockerbie.id.au, 2015
 **/

function role_tag_admin_allowed($w, $path) {
	return $w->checkUrl($path, "tag", null, "*");
}

function role_tag_user_allowed($w, $path) {
	return $w->checkUrl($path, "tag", null, "ajaxAddTag") || 
		   $w->checkUrl($path, "tag", null, "ajaxCreateTag") || 
		   $w->checkUrl($path, "tag", null, "ajaxGetTags") || 
		   $w->checkUrl($path, "tag", null, "ajaxRemoveTag") ||
		   $w->checkUrl($path, "tag", null, "changeTags");
}
