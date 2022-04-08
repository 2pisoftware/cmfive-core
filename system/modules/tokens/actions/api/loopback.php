<?php

/**@author Derek Crannaford */

function loopback_ALL(Web $w)
{
    $testData = [
        'POST' => TokensService::getInstance($w)->getJsonFromPostRequest()
    ];

    ApiOutputService::getInstance($w)->apiKeyedResponse($testData, "API loopback endpoint reached");
}
