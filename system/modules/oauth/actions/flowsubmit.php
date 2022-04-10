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
    $known->delete();

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

            $appCheck = TokensService::getInstance($w)->getAppFromJwtPayload($check['access_token']);
            if ($appCheck !== $known->app_id) {
                ApiOutputService::getInstance($w)->apiFailMessage("oauth flow response", "Client conflict");
            }

            if (!empty($app['splashpage'])) {
                $payload = TokensService::getInstance($w)->getJwtPayload($check['access_token']);
                $userDisplay = $payload["username"] ?? null;
                $userDisplay .= (empty($check['email'])) ? "" : ((empty($userDisplay)) ? "" : " : " . $check['email']);
                $template = OauthFlowService::getInstance($w)->getOauthSplashPageTemplate($app['splashpage']);
                if (!empty($template)) {
                    $splashPage = TemplateService::getInstance($w)->render(
                        $template->id,
                        [
                            "display" => $userDisplay,
                            "app" => $app,
                            "jwt" => $check,
                            "payload" => $payload
                        ]
                    );
                    ApiOutputService::getInstance($w)->apiReturnCmfiveStyledHtml($w, $splashPage);
                }
            }

            ApiOutputService::getInstance($w)->apiKeyedResponse($check, "Application API key granted for " . $app['title']);
        }
    }

    ApiOutputService::getInstance($w)->apiFailMessage("oauth flow response", "No handler");
}
