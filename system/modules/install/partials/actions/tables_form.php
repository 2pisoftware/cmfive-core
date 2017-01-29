<?php
    
function tables_form_ALL(Web &$w, $params)
{
    //error_log("PARAMS: " . print_r($params, true));
    
    $w->ctx("more",        isset($params['more']) ? $params['more'] : '');
    $w->ctx("db_host",     $_SESSION['install']['saved']['db_host']);
    $w->ctx("db_username", $_SESSION['install']['saved']['db_username']);
    $w->ctx("app",         strtolower($_SESSION['install']['saved']['application_name']));
    $w->ctx("name",        isset($params['name']) ? $params['name'] : 'db_database');
}
