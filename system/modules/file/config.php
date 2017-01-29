<?php

Config::set('file', [
	'version' => '0.8.0',
    'active' => true,
    'path' => 'system/modules',
    'fileroot' => dirname(__FILE__) . '/../uploads',
    'topmenu' => false,
    'search' => ["File Attachments" => "Attachment"],
    "dependencies" => [
        "knplabs/gaufrette" => "0.2.*@dev",
		"aws/aws-sdk-php" => "~2",
        "dropbox-php/dropbox-php" => "*"
    ],
	'hooks' => [
		'admin'
	],
	'adapters' => [
		'local' => [
			'active' => true
		],
		'memory' => [
			'active' => false
		],
		's3' => [
			'active' => false,
			'key' => '',
			'secret' => '',
			'bucket' => '',
			'options' => []
		],
		'dropbox' => [
			'active' => false,
			'app_id' => ''
		]
	]
]);
