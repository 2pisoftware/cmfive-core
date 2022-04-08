<?php
/**@author Derek Crannaford */

function info_ALL(Web $w)
{
     $testData = [
        'url term' => "/tokens-api/",
        'info' => "Service details",
        'test' => "Test GET endpoint",
        'loopback' => "Test POST endpoint"
    ];


    ApiOutputService::getInstance($w)->apiKeyedResponse($testData,"API test service");
}
