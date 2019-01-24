<?php

Config::set('file', [
	'active' => true,
    'path' => 'system/modules',
    'fileroot' => dirname(__FILE__) . '/../uploads',
    'topmenu' => false,
    'search' => ["File Attachments" => "Attachment"],
    "dependencies" => [
        "knplabs/gaufrette" => "0.4.*@dev",
		"aws/aws-sdk-php" => "3.29.*"
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
			'region' => 'ap-southeast-2',
			'version' => '2006-03-01',
			'credentials' => [
				'key' => '',
				'secret' => ''
			],
			'bucket' => '',
			'options' => [
				'create' => true
			]
		]
	]
]);
