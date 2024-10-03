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
        "symfony/mailer" => "^7.1.5",
        "twig/twig" => "^3.14.0",
        "nesbot/carbon" => "^3.8.0",
        "robmorgan/phinx" => "^0.16.4",
        "softark/creole" => "^1.3",
        "monolog/monolog" => "^3.7.0",
        "aws/aws-sdk-php" => "3.322.10",
        "aws/aws-php-sns-message-validator" => "^1.9",
        "maxbanton/cwh" => "^2.0.4"
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
        ],
        // This value comes from \Monolog\Logger::DEBUG constant.
        "level" => 100,
    ],
    "mail" => [
        "aws" => [
            "credentials" => [
                "key" => "",
                "secret" => "",
            ],
            "queue_url" => "",
            "region" => "",
            "version" => "",
            // An array of domains that have been validated in AWS, eg. example.com.
            "validated_domains" => [],
        ],
    ],
]);
