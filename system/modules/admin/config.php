<?php

Config::set('admin', [
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'audit_ignore' => ["index"],
    'hooks' => [
        'core_dbobject',
        'core_web'
    ],
    'printing' => [
        'command' => [
            'unix' => 'lpr -P $printername $filename',
            // 'windows' => 'C:\Users\adam\Desktop\SumatraPDF-2.4\SumatraPDF.exe -print-to $printername $filename'
        ]
    ],
    'database' => [
        'output' => 'sql',
        'command' => [
            'unix' => 'mysqldump -u $username -p\'$password\' $dbname | gzip > $filename.gz',
            'windows' => 'C:\\Ampps\\mysql\\bin\\mysqldump.exe -u $username -p$password $dbname > $filename'
        ]
    ],
    "dependencies" => [
        "swiftmailer/swiftmailer" => "~6.2",
        "twig/twig" => "2.4.*",
        "nesbot/carbon" => "1.22.1",
        "robmorgan/phinx" => "0.8.*",
        "sendgrid/sendgrid" => "~5.5",
        "softark/creole" => "~1.2",
        "monolog/monolog" => "^1.22",
        "aws/aws-sdk-php" => "^3.24",
        "aws/aws-php-sns-message-validator" => "^1.1",
        "maxbanton/cwh" => "^1.0"
    ],
    "bulkemail" => [
        "number_per_cron" => 5,
        //set user to authenticate attachments for emails
        "auth_user" => null
    ],
    'logging' => [
        'target' => 'file',         // Can be 'file' or 'aws' (cloudwatch]
        'retention_period' => 30,   // In number of days
        'cloudwatch' => [
            'group_name' => 'cmfive-app-logs',
            'stream_name_app' => 'CmfiveApp',
            'region'    => 'ap-southeast-2',
            'version'   => 'latest',
        ]
    ],
    "mail" => [
        "http" => [
            "mode" => "production",
            "profile" => "",
            "region" => "",
        ],
    ],
]);
