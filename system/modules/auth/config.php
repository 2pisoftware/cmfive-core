<?php

Config::set('auth', array(
    'migration_version' => '0.8.0',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'access_hint' => 'Forgot password?',
    'hooks' => [
        'admin'
    ]
));
