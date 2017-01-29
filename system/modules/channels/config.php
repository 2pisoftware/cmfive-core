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
        "zendframework/zend-mail" => "2.2.5",
        "zendframework/zend-serializer" => "2.2.5"
    )
));
