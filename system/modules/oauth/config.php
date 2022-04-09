<?php
Config::set('oauth', array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'hooks' => [
        'auth',
        'tokens',
        'oauth'
    ],
    'apps' => [
        'cognito' => [
            'client_id#1' => [
                'title' => "Cmfive limited API",
                'domain' => ""
            ],
            'client_id#2' => [
                'title' => "Cmfive special API",
                'domain' => ""
            ],
            '1kssj2bp4ospjfna33if60s8k7' => [
                'client_secret' => "1b35bugv6besills2rk1mm8s5dhkj45magsvhigatlnbc81ncuts",
                'title' => "Proof of arbitration",
                'domain' => "2pi-devpoint-arbitration.auth.ap-southeast-2.amazoncognito.com",
                'scope' => "aws.cognito.signin.user.admin"
            ],

        ]
    ],
));
