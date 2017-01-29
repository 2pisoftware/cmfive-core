<?php

// given user interface employs tab, templates control display of tabs based on user role.

function role_report_admin_allowed(Web $w,$path) {
    return preg_match("/report(-.*)?\//",$path);
}

function role_report_editor_allowed(Web $w,$path) {
    return $w->checkUrl($path, "report", null, "*");
}

function role_report_user_allowed(Web $w,$path) {
    return $w->checkUrl($path, "report", null, "*");
}

