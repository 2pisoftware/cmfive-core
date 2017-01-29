<?php

Config::set('install', array(
	'version' => '0.8.3',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
                             
    'default' => array(
        'application_name' => 'CmFive',
        'company_name' => '2pi Software',
        'company_url' => 'http://2pisoftware.com',

        'timezone' => 'Australia/Sydney',
        'gmt' => 11,

        'email_transport' => 'smtp',
        'email_smtp_host' => 'smtp.gmail.com',
        'email_smtp_port' => 465,
        'email_auth' => true,
        'email_encryption' => 'ssl',
        'email_test_send' => 'company_support_email',
                       
        'db_driver' => 'mysql',
        'db_host' => 'localhost',
        'db_port' => 3306,
        'db_user_type' => 'existing',
                       
        'admin_email_type' => 'company_support_email'
                       
        // tables, rest api, users?
    )
));
