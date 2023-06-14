<?php

Config::set('inbox', [
    'active' => false,
    'path' => 'system/modules',
    'topmenu' => true,
    'hooks' => ['admin', 'inbox'],
]);
