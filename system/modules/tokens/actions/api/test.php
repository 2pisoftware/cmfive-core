<?php
/**@author Derek Crannaford */

function test_ALL(Web $w)
{

    $testData = [
        'First' => "A",
        'Second' => "B",
        'Third' => "C"
    ];


    ApiOutputService::getInstance($w)->apiKeyedResponse($testData,"API test endpoint reached");

}
