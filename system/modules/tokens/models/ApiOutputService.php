<?php

/**@author Derek Crannaford */

class ApiOutputService extends DbService
{
    public function useNoTemplate($w)
    {
        $w->setLayout(null);
    }

    public function apiReturnJsonResponse($response)
    {
        $this->useNoTemplate($this->w);

        // mark header for return content type JSON
        if (substr($response['status'],0,1) == "2") {
            header('Content-Type: application/json');
        } else {

            $this->w->Log->info("API request rejected: ".$response['referer']);
            header($_SERVER["SERVER_PROTOCOL"] . " " . $response['status'] . " " . $response['payload'][0]);
        }

        echo json_encode(['response' => $response]);
        exit(0);
    }

    // JSON simple message
    public function apiKeyedResponse($payload = [], $message = "Success", $status_code = "200")
    {
        $success = [
            'status' => $status_code,
            'message' => $message,
            'payload' => $payload
        ];
        $this->apiReturnJsonResponse($success);

    }

    // JSON simple message
    public function apiSimpleResponse($detail = "", $message = "Success", $status_code = "200")
    {
        $payload[] = $detail;
        $success = [
            'status' => $status_code,
            'message' => $message,
            'payload' => $payload
        ];
        $this->apiReturnJsonResponse($success);

    }

    // JSON nice fail message
    public function apiFailMessage($source = "", $detail = "", $message = "Failure", $status_code = "500")
    {
        $payload[] = $detail;
        $errors = [
            'status' => $status_code,
            'referer' => $source,
            'message' => $message,
            'payload' => $payload
        ];        
        $this->apiReturnJsonResponse($errors);

    }

    // JSON nice refuse message
    public function apiRefuseMessage($source = "", $detail = "", $message = "Unauthorised", $status_code = "403")
    {
        $payload[] = $detail;
        $errors = [
            'status' => $status_code,
            'referer' => $source,
            'message' => $message,
            'payload' => $payload
        ];   
        
        $this->apiReturnJsonResponse($errors);
    }
}
