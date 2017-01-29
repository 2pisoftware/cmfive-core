<?php
/**
 * config settings for favorites 
 *
 * @author Steve Ryan, steve@2pisoftware.com, 2015
 **/

Config::set('favorite', array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'widgets' => array(
		'favorites_widget'
	),
	'hooks' => array(
		'core_template'
	)
));
