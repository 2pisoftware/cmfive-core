<?php

Config::set('report', array(
    'version' => '0.8.0',
    'active' => true,
    'path' => 'system/modules',
    "dependencies" => array(
        "tecnickcom/tcpdf" => "~6.2",
        "parsecsv/php-parsecsv" => "1.2.0"
    ),
    '__password' => 'maybeconsiderchangingthis',
	'topmenu' => true,
	'hooks' => [
		'admin'
	]
));
