<?php

Config::set('main', [
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'application_name' => 'cmfive',
    'company_name' => 'cmfive',
    'company_url' => 'https://github.com/2pisoftware',
    "dependencies" => [
        // "monolog/monolog" => "1.22.*@dev",
        "scssphp/scssphp" => "1.9.0"
    ],
    'hooks' => [
        'core_dbobject',
        'admin'
    ],
    'available_languages' => [
        'en_AU' => 'English',
        'de_DE' => 'Deutsch',
        'fr_FR' => 'Français',
    ],
    'datepicker_first_day' => 0, /* Set the first day of the week for datepickers, integer between 0 and  6, where 0 is sunday, 1 is monday, etc.*/
]);
