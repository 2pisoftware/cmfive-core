<?php

Config::set('report', [
    'active' => true,
    'path' => 'system/modules',
    "dependencies" => array(
        "tecnickcom/tcpdf" => "~6.2",
        "parsecsv/php-parsecsv" => "1.2.0",
    ),
    '__password' => 'maybeconsiderchangingthis',
    'topmenu' => true,
    'hooks' => [
        'admin',
    ],
    'database' => [
        'hostname'  => '',
        'username'  => '',
        'password'  => '',
        'database'  => '',
        'driver'    => ''
    ]
]);
