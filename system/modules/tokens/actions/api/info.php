<?php
/**@author Derek Crannaford */

function info_ALL(Web $w)
{
    ApiOutputService::getInstance($w)->useNoTemplate($w);
    echo "test | loopback | info";
}