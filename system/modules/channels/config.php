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
        "laminas/laminas-mail" => "~2.9",
        "laminas/laminas-serializer" => "~2.9"
    ]
]);
