<?php

/**@author Derek Crannaford */

function grant_ALL(Web $w)
{

    ApiOutputService::getInstance($w)->apiSimpleResponse("NO_TOKEN", "No Token issuer is enabled");

    /*
    $granted = TokensService::getInstance($w)->getDayDateUserToken($w);

    ApiOutputService::getInstance($w)->apiSimpleResponse($granted, "Single day user API key granted");
    */
}
