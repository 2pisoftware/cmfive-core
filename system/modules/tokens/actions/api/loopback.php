<?php
/**@author Derek Crannaford */

function loopback_ALL(Web $w)
{  
    ApiOutputService::getInstance($w)->useNoTemplate($w);  
    echo "should mirror inputs";
}
