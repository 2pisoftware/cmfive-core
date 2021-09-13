<?php

/**
 * Config settings for favorites 
 *
 * @author Steve Ryan, steve@2pisoftware.com, 2015
 **/

Config::set('favorite', [
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'widgets' => [
        'favorites_widget'
    ],
    'hooks' => [
        'core_template',
        'core_dbobject'
    ]
]);
