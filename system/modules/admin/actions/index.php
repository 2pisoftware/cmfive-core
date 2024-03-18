<?php

function index_ALL(Web &$w)
{
    $w->setLayout('layout-bootstrap-5');
 
    AdminService::getInstance($w)->navigation($w, "Dashboard");
    $w->ctx("currentUsers", AuditService::getInstance($w)->getLoggedInUsers());
}
