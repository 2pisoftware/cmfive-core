<?php
/**@author Derek Crannaford */

function grant_ALL(Web $w)
{
    ApiOutputService::getInstance($w)->useNoTemplate($w);

    echo print_r(TokensService::getInstance($w)->getDayDateUserToken($w),true);
    exit(1);
}
