<?php

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Aws\Sqs\SqsClient;

class HttpTransport implements GenericTransport
{
    private $w;
    private $transport;

    public function __construct($w, $layer)
    {
        $this->w = &$w;
        $this->transport = $this->getTransport($layer);
    }

    public function getTransport($layer)
    {
        if (!empty($this->transport)) {
            return $this->transport;
        }

        $base_uri = Config::get("email.base_uri");
        if (empty($base_uri)) {
            return;
        }

        return new Client([
            "base_uri" => $base_uri,
            "timeout" => 5,
        ]);
    }

    public function send($to, $reply_to, $subject, $body, $cc = null, $bcc = null, $attachments = [], $headers = [])
    {
        $client = SqsClient::factory([
            "profile" => "",
            "region" => "",
        ]);


        $send_uri = Config::get("email.send_uri");
        if (empty($send_uri)) {
            $this->w->Log->error("Failed to send mail to: $to, from: $reply_to, about: $subject: email.send_uri not set in config");
            return;
        }

        if (empty($to) || strlen($to) === 0) {
            $this->w->Log->error("Failed to send mail to: $to, from: $reply_to, about: $subject: no recipients");
            return;
        }

        if ($this->transport === null) {
            $this->w->Log->error("Failed to send mail to: $to, from: $reply_to, about: $subject: no email transport defined");
            return;
        }

        if (strpos($to, ",") !== false) {
            $to = array_map("trim", explode(",", $to));
        }

        if (strpos($cc, ",") !== false) {
            $cc = array_map("trim", explode(",", $cc));
        }

        if (strpos($bcc, ",") !== false) {
            $bcc = array_map("trim", explode(",", $bcc));
        }

        if (empty($cc)) {
            $cc = [];
        }

        if (empty($bcc)) {
            $bcc = [];
        }

        $attachment_files = [];

        foreach ($attachments as $attachment) {
            if (!file_exists($attachment)) {
                continue;
            }

            $file_contents = file_get_contents($attachment);
            if ($file_contents === false) {
                $this->w->Log->setLogger("MAIL")->error("Unable to get contents from attachment: $attachment");
                continue;
            }

            $attachment_file = [
                "filename" => basename($attachment),
                "data" => file_get_contents($attachment),
                "inline" => false,
            ];

            $attachment_files[] = $attachment_file;
        }

        $request_body = [
            "from" => [
                "Name" => "",
                "Address" => "",
            ],
            "to" => is_array($to) ? $to : [$to],
            "cc" => is_array($cc) ? $cc : [$cc],
            "bcc" => is_array($bcc) ? $bcc : [$bcc],
            "reply_to" => $reply_to,
            "subject" => $subject,
            "body" => $body,
            "body_content_type" => "text/html",
            "headers" => $headers,
            "attachments" => $attachment_files,
        ];

        try {
            $response = $this->transport->request("POST", $send_uri, [
                RequestOptions::HEADERS => [
                    "content-type" => "application/json",
                    "x-api-key" => Config::get("email.api_key"),
                ],
                RequestOptions::BODY => json_encode($request_body),
            ]);

            if ($response->getStatusCode() != 200 && $response->getStatusCode() != 202) {
                throw new Exception("unexpected status code returned: {$response->getStatusCode()}");
            }
        } catch (Exception $e) {
            $this->w->Log->error("Failed to send mail to: $to, from: $reply_to, about: $subject: {$e->getMessage()}");
        }
    }
}
