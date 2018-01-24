<?php

function role_task_admin_allowed(Web $w, $path) {
    return $w->checkUrl($path, "task", "*", "*");
}

function role_task_user_allowed(Web $w, $path) {
    return 
        $w->checkUrl($path, "task", null, "index") || 
        $w->checkUrl($path, "task", null, "tasklist") ||
        $w->checkUrl($path, "task", null, "edit") ||
		$w->checkUrl($path, "task", null, "delete") ||
        $w->checkUrl($path, "task", null, "duplicatetask") ||
        $w->checkUrl($path, "task", null, "ajaxGetExtraDetails") ||
        $w->checkUrl($path, "task", null, "ajaxGetFieldForm") || 
        $w->checkUrl($path, "task", null, "addtime") || 
        $w->checkUrl($path, "task", null, "edittime") || 
        $w->checkUrl($path, "task", null, "updateusergroupnotify") || 
        $w->checkUrl($path, "task", null, "updateusertasknotify") || 
        $w->checkUrl($path, "task", null, "taskweek") ||
        $w->checkUrl($path, "task", null, "deletetime") ||
        $w->checkUrl($path, "task", null, "taskAjaxSelectbyTaskGroup") ||
        $w->checkUrl($path, "task", null, "ical") ||
		$w->checkUrl($path, "task", "subscriber", "*") ||
		$w->checkUrl($path, "task", "group", "ajaxAutocompleteTaskgroups");
}

function role_task_group_allowed(Web $w, $path) {
    return $w->checkUrl($path, "task", "group", "*"); //preg_match("/task-group\//", $path);
}


