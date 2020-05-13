<?php

Config::set('auth', array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'access_hint' => 'Forgot password?',
    'hooks' => [
        'admin'
    ],
    "dependencies" => [
        "sonata-project/google-authenticator" => "^2.2"
    ]
));
