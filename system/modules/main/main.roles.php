<?php

function role_user_allowed(Web $w,$path) {
	return $w->checkUrl($path, "main", "*", "*") || 
           $w->checkUrl($path, "auth", "*", "*") ||
           $w->checkUrl($path, "search", "*", "*") || 
		   $w->checkUrl($path, "form", "instance", '*');
//    $include = array(
//        "main",
//        "auth",
//    	"search"
//    );
//    
//    $path_explode = explode("/", $path);
//    $module = $path_explode[0];
//    // $action = !empty($path_explode[1]) ? $path_explode;
//    $allowed = in_array($module,$include);
//    return $allowed;
}
