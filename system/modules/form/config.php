<?php

Config::set('form', [
	'active' => true,
	'topmenu' => true,
	'path' => 'system/modules',
	'hooks' => [
		'core_template'
	],
	'interfaces' => [
		'FormStandardInterface'
	],
	'mapping' => []
]);