<?php
    
function database_VAR()
{
    return array(
        ValidationService::add('db_driver')->options(array('mysql')),
        ValidationService::add('db_host')->type('url'),
        ValidationService::add('db_port')->type('integer')->min(0)->max(65535),
        ValidationService::add('db_user_type')->options(array('existing', 'new')),
        ValidationService::add('db_username')->required(),
        ValidationService::add('db_password')
    );
}

function database_POST(Web &$w) {
    //$step = getInstallStep('database');
    
     // error checking and saving to session from $_POST
    $w->Install->saveInstall('database');

    // success message
    return "Configured Database Conection";
}
