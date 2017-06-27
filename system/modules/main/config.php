<?php

Config::set('main', array(
	'version' => '0.8.0',
	'active' => true,
	'path' => 'system/modules',
	'topmenu' => false,
	'application_name' => 'cmfive',
	'company_name' => 'cmfive',
	'company_url' => 'http://github.com/careck/cmfive',
	"dependencies" => array(
		"monolog/monolog" => "1.22.*@dev",
	),
	'hooks' => [
		'core_dbobject',
	],
	'available_languages' => [
		'en_AU' => 'English',
		'de_DE' => 'Deutsche',
		'fr_FR' => 'Français',
	],
));
