<?php
Config::set('oauth', array(
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => false,
    'dependencies' => [
        'aws/aws-sdk-php' => '^3.55'
    ],
    'hooks' => [
        'auth',
        'tokens',
        'oauth'
    ],
    'apps' => [
        'cognito' => [
            // 'client_id#1' => [
            //     'client_secret' => "abcdeXYZ123",
            //     'title' => "MyAppIsCalled",
            //     'domain' => "2pi-something.somewhere.auth.ap-southeast-2.amazoncognito.com",
            //     'scope' => "aws.cognito.signin.user.admin",
            //     'callback' => "https://pi-HostingSomething.somewhere/oauth/flowsubmit",
            //     'splashpage' => "TemplateTitle" (module:oauth, category:splashpage)
            // ],
            // 'client_id#2' => [
            //     'client_secret' => "abcdeXYZ123",
            //     'title' => "MyOtherAppIsCalled",
            //     'domain' => "2pi-something.somewhere.auth.ap-southeast-2.amazoncognito.com",
            //     'scope' => "aws.cognito.signin.user.admin",
            //     'callback' => "https://pi-HostingSomething.somewhere/oauth/flowsubmit",
            // ],
            '1kssj2bp4ospjfna33if60s8k7' => [
                'client_secret' => "1b35bugv6besills2rk1mm8s5dhkj45magsvhigatlnbc81ncuts",
                'title' => "Proof of arbitration",
                'domain' => "2pi-devpoint-arbitration.auth.ap-southeast-2.amazoncognito.com",
                'scope' => "aws.cognito.signin.user.admin",
                'callback' => "https://apatchofnettles.github.io/",
                'splashpage' => "Prover",
                'user_pool' => "ap-southeast-2_4c9kTZput"
            ],

        ]
    ],
));

/*
/////////
// 'splashpage' => "Prover" -> templates -> oauth -> splashpage
/////////
<div>
<br><br>
<h2 align="center">
  API application:
</h2>
<h1 align="center">
    {{app["title"]}}
    </h1>
<h2 align="center">
  Grants access for:
</h2>
<h1 align="center">
    {{display}}
</h1>
<h2 align="center">
  Token is:
</h2>
<div style="padding: 0 20% 0 20%;">
  <code style="display: block;overflow-wrap: break-word;text-align: left;">
    {{jwt["access_token"]}}
  </code>
</div>
<br>
</div>
*/