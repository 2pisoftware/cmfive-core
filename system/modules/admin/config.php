<?php

Config::set('admin', array(
    'version' => '0.8.0',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'audit_ignore' => array("index"),
    'hooks' => array('core_dbobject','core_web'),
    'printing' => array(
        'command' => array(
            'unix' => 'lpr -P $printername $filename',
            // 'windows' => 'C:\Users\adam\Desktop\SumatraPDF-2.4\SumatraPDF.exe -print-to $printername $filename'
        )
    ),
    'database' => array(
        'output' => 'sql',
        'command' => array(
            'unix' => 'mysqldump -u $username -p\'$password\' $dbname | gzip > $filename.gz',
            // 'windows' => 'J:\\xampp\\mysql\\bin\\mysqldump.exe -u $username -p$password $dbname > $filename'
        )
		// Example to back up off site (only dropbox support currently)
		// 'backuplocations' => ['dropbox' => ['key' => '<KEY>', 'secret' => '<SECRET>']]
    ),
    "dependencies" => array(
        "swiftmailer/swiftmailer" => "5.4.*",
        "twig/twig" => "2.4.*",
        "nesbot/carbon" => "1.22.1",
		"robmorgan/phinx" => "0.8.*",
		"sendgrid/sendgrid" => "~5.5"
    ),
    "bulkemail"=> array(
        "number_per_cron" => 5,
        //set user to authenticate attachments for emails
        "auth_user" => null
    )
));

//======= define openssl encryption key and initialization vector ============
Config::set("openssl", [
    "key" => "lvewfopkkzsxnjjws1zc66rucgh8lt",
    "iv"  => "ash17hr39fu12cva"
]);