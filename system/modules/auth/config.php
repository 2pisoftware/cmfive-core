<?php

Config::set('auth', array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'access_hint' => 'Forgot password?',
    'hooks' => [
        'admin'
    ],
    'show_application_name' => true,
    "dependencies" => [
        "sonata-project/google-authenticator" => "^2.2"
    ]
));
