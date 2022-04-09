<?php

/**@author Derek Crannaford */

function flowsubmit_ALL(Web $w)
{

    if (empty($_GET['code']) || empty($_GET['state'])) {
        ApiOutputService::getInstance($w)->apiFailMessage("oauth flow response", "Flow is invalid");
    }

    $known = OauthFlowService::getInstance($w)->getObject("OauthFlow", ['state' => $_GET['state']]);
    $app = OauthFlowService::getInstance($w)->getOauthAppById($known->app_id ?? null);
    if (empty($known) || empty($app)) {
        ApiOutputService::getInstance($w)->apiFailMessage("oauth flow response", "State is invalid");
    }

    $asserted = $w->callHook(
        "oauth",
        "request_code_submit_flow",
        [
            'code' => $_GET['code'],
            'state' => $_GET['state']
        ]
    );

    foreach ($asserted as $check) {
        if (!empty($check['access_token'])) {
            ApiOutputService::getInstance($w)->apiKeyedResponse($check, "Application API key granted for " . $app['title']);
        }
    }

    ApiOutputService::getInstance($w)->apiFailMessage("oauth flow response", "No handler");
}

// eg: https://2piXYZ/tokens/flowtoken/?code=ed32b88f-eada-4e42-bc3e-591c05f4ad43