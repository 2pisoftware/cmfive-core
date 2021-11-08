<?php

Config::set('auth', [
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
    ],
    'login' => [
        'password' => [
            'enforce_length' => false,
            'min_length' => 8
        ],
        'attempts' => [
            'track_attempts' => false,
            'max_attempts' => 5
        ]
    ],
    'logout' => [
        'logout_after_inactivity' => false,
        'timeout' => 900
    ],
    'login_label' => 'Login'
]);
