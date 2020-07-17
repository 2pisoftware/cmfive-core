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
    	'ExternalFormXMLProcessor'
	],
	'components' => [
		'metadata-subform' => ['/system/modules/form/assets/js/metadata-subform.vue.js']
	],
	'form_event_processors' => [
		'EmailNotificationEventProcessor'
	]
]);
