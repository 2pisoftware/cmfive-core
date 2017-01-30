<?php

Config::set('main', array(
    'version' => '0.8.0',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'application_name' => 'cmfive',
    'company_name' => 'cmfive',
    'company_url' => 'http://github.com/2pisoftware/cmfive',
    "dependencies" => array(
        "monolog/monolog" => "1.8.*@dev"
    ),
	'hooks' => [
		'core_dbobject'
	]
));
