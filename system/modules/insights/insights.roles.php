<?php


function role_insights_admin_allowed(Web $w,$path) {
    return $w->checkUrl($path, "insights", "*", "*");
}
function role_insights_user_allowed(Web $w,$path) {
    return $w->checkUrl($path, "insights", "*", "*");
}
