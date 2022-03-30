<?php

/**@author Derek Crannaford */

class ApiOutputService extends DbService
{
   
    public function useNoTemplate($w)
    {
        $w->setLayout(null);
        // mark header for return content type JSON
    }

    // JSON nice fail message
    // JSON nice refuse message
    

}