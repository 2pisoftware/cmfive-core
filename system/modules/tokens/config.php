<?php
Config::set('tokens', array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'hooks' => [
        'auth',
        'tokens'
    ],
));
