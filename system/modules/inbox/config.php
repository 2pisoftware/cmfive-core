<?php

Config::set('inbox', array(
    'version' => '0.8.0',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'hooks' => array('admin', 'inbox'),
));
