<?php

/**@author Derek Crannaford */

function flowinit_ALL(Web $w)
{

    $app = $w->pathMatch('app')['app'] ?? null;
    if (empty($app)) {
        ApiOutputService::getInstance($w)->apiFailMessage("oauth flow intialisation", "App not valid");
    }
    
    $w->callHook(
        "oauth",
        "request_app_id_flow",
        [
            'app_id' => $app
        ]
    );
    
    ApiOutputService::getInstance($w)->apiFailMessage("auth flow intialisation", "No handler");
}

// eg: http://localhost:3000/oauth/flowinit/1kssj2bp4ospjfna33if60s8k7