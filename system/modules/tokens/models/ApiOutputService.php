<?php

/**@author Derek Crannaford */

class ApiOutputService extends DbService
{
    // This function, having no apparent need for elaboration
    // was retired for feature into 8.1, beware if you used it externally!
    // public function useNoTemplate($w)
    // {
    //     $w->setLayout(null);
    // }

    public function apiReturnJsonResponse($response)
    {
        $this->w->setLayout(null);
        http_response_code($response['status']);
        header('Content-Type: application/json');

        if (substr($response['status'], 0, 1) != "2") {
            LogService::getInstance($this->w)->info("API request rejected: " . $response['referer']);
            // Don't need, already have set response code!
            // header($_SERVER["SERVER_PROTOCOL"] . " " . $response['status'] . " " . $response['payload'][0]);
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
