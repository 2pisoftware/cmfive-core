<?php
/**@author Derek Crannaford */

function test_ALL(Web $w)
{
    ApiOutputService::getInstance($w)->useNoTemplate($w);
    echo "A B C";

    
}
