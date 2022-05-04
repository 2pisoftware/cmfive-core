<?php

function index_ALL(Web &$w) {
	AdminService::getInstance($w)->navigation($w,"Dashboard");
	$w->ctx("currentUsers", AuditService::getInstance($w)->getLoggedInUsers());  
}
