<?php

/**@author Derek Crannaford */

function grant_ALL(Web $w)
{
    $granted = TokensService::getInstance($w)->getDayDateUserToken($w);

    ApiOutputService::getInstance($w)->apiSimpleResponse($granted, "Single day user API key granted");
}
