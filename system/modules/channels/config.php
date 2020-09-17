<?php

Config::set('channels', array(
	'version' => '0.8.0',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    '__password' => 'maybeconsiderchangingthis',
    'processors' => array(
    	'TestProcessor'
    ),
    "dependencies" => array(
        "laminas/laminas-mail" => "~2.9",
        "laminas/laminas-serializer" => "~2.9"
    )
));
