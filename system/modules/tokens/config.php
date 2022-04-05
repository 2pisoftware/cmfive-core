<?php
Config::set('tokens', array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'hooks' => [
        'auth',
        'tokens'
    ],
));
