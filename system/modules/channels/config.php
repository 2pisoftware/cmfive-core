<?php

Config::set('channels', [
    'version' => '0.8.0',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    '__password' => 'maybeconsiderchangingthis',
    'processors' => [
        'TestProcessor'
    ],
    "dependencies" => [
        "zendframework/zend-mail" => "~2.9",
        "zendframework/zend-serializer" => "~2.9"
    ]
]);
