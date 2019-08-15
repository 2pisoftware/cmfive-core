<?php

Config::set('file', [
	'active' => true,
    'path' => 'system/modules',
    'fileroot' => dirname(__FILE__) . '/../uploads',
    'topmenu' => false,
    "dependencies" => [
        "knplabs/gaufrette" => "~0.8", 	
		"aws/aws-sdk-php" => "~3.69"
		// watch this space: phpthumb ver2 has dropped support for file content objects?
		// if support is fixed, should start using composer/ver2 instead of LIB/ver1
		//"masterexploder/phpthumb" => "~2.1"	 
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
	],
	'docx_viewing_window_duration' => 0,
]);
