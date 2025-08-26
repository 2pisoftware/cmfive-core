<?php

/**
 * config settings for tags
 *
 * @author Robert Lockerbie, robert@lockerbie.id.au, 2015
 **/

Config::set('tag', array(
	'active' => true,
	'path' => 'system/modules',
	'topmenu' => true,
	'hooks' => [
		'core_dbobject'
	]
));
