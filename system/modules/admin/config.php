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
        "swiftmailer/swiftmailer" => "@stable",
        "twig/twig" => "1.*",
        "nesbot/carbon" => "1.14",
		"mandrill/mandrill" => "1.0.*",
		"robmorgan/phinx" => "^0.4.6",
		"sendgrid/sendgrid" => "~4.0"
    ),
    "bulkemail"=> array(
        "number_per_cron" => 5,
        //set user to authenticate attachments for emails
        "auth_user" => null
    )
));
