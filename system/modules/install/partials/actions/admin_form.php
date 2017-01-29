<?php
    
function admin_form_ALL(Web &$w, $params)
{
    //error_log("PARAMS: " . print_r($params, true));
    
    $w->ctx("id",                isset($params['id'])        ? $params['id']         : 'new');
    $w->ctx("admin_username",    isset($params['login'])     ? $params['login']      : $_SESSION['install']['saved']['admin_username']);
    $w->ctx("admin_firstname",   isset($params['firstname']) ? $params['firstname']  : $_SESSION['install']['saved']['admin_firstname']);
    $w->ctx("admin_lastname",    isset($params['lastname'])  ? $params['lastname']   : $_SESSION['install']['saved']['admin_lastname']);
    $w->ctx("admin_email",       isset($params['email'])     ? $params['email']      : $_SESSION['install']['saved']['admin_email']);
    $w->ctx("admin_password",   !isset($params['id'])        ? $_SESSION['install']['saved']['admin_password'] : '');

    $w->ctx("admin_email_type",  isset($params['id']) &&
                                 isset($params['email'])     ? (strcmp($_SESSION['install']['saved']['company_support_email'],
                                                                $params['email']) === 0 ? 'company_support_email' : 'admin_email') :
                                                                $_SESSION['install']['saved']['admin_email_type']);
    
    // extract the domain name from the company url
    $w->ctx("domain",            empty($_SESSION['install']['saved']['company_url']) ? 'example.com' :
                                    parse_url($_SESSION['install']['saved']['company_url'], PHP_URL_HOST));
    
    // should always be set
    $w->ctx("company_support_email", $_SESSION['install']['saved']['company_support_email']);
    
    
    $w->ctx("msg",               isset($params['msg'])        ? $params['msg']         :
            "This will create the system's first admin user in <b>\"" . $_SESSION['install']['saved']['db_database'] .
            "\"</b> and assign them full priviledges. After logging into this new account, upon completion of " .
            "cmfive's installation, the system can be managed and new users created with specific priveledges. " .
            "This user should, however, represent, a real person.");
}
