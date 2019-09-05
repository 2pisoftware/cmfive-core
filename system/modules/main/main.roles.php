<?php

function role_user_allowed(Web $w,$path) {
	return $w->checkUrl($path, "main", "*", "*") || 
           $w->checkUrl($path, "auth", "*", "*") ||
           $w->checkUrl($path, "search", "*", "*") || 
		   $w->checkUrl($path, "form", "instance", '*');
}

function role_restrict_allowed(Web $w, $path) {
	return $w->checkUrl($path, "main", "*", "*");
}