<?php

Config::set('form', [
	'active' => true,
	'topmenu' => true,
	'path' => 'system/modules',
	'hooks' => [
		'core_template',
		'core_dbobject'
	],
	'interfaces' => [
		'FormStandardInterface',
		'FormAdditionalFieldsInterface'
	],
	'processors' => [
    	'ExternalFormProcessor'
	],
]);