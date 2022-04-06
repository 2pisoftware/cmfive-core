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
    public function apiFailMessage($w, $source="", $detail="", $status_code="500", $title="Failure")
    {
        $errors = array('status'=> $status_code,
                        'source' => $source,
                        'title' => $title,
                        'detail'=> $detail);

        $response = array('errors' => $errors);

        echo json_encode($response);
    }

    // JSON nice refuse message
    public function apiRefuseMessage($w, $source="", $detail="", $status_code="403", $title="Unauthorised")
    {
        $errors = array('status'=> $status_code,
                        'source' => $source,
                        'title' => $title,
                        'detail'=> $detail);
        $response = array('errors' => $errors);

        echo json_encode($response);
    }
}
