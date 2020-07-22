<?php

use GuzzleHttp\Client;
use Aws\Sqs\SqsClient;

class AwsTransport implements GenericTransport
{
    private $w;

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

    /**
     * Send email data to SQS. Currently all attachments have to be stored in S3 to avoid breaking changes to
     * this method's API.
     *
     * @param string $to
     * @param string $reply_to
     * @param string $subject
     * @param string $body
     * @param string|null $cc
     * @param string|null $bcc
     * @param array $attachments
     * @param array $headers
     *
     * @return void
     */
    public function send($to, $reply_to, $subject, $body, $cc = null, $bcc = null, $attachments = [], $headers = [])
    {
        $client = null;

        $region = Config::get("admin.mail.http.region");
        if (empty($region)) {
            $this->w->Log->error("Failed to send mail to: admin.mail.http.region not set in config");
            return;
        }

        // TODO: Handle production.
        if (Config::get("system.environment") === "development") {
            $credentials = Config::get("admin.mail.http.credentials");
            if (empty($credentials)) {
                $this->w->Log->error("Failed to send mail to: admin.mail.http.credentials not set in config");
                return;
            }

            $client = new SqsClient([
                "credentials" => [
                    "key" => $credentials["key"],
                    "secret" => $credentials["secret"],
                ],
                "region" => $region,
                "version" => "2012-11-05",
            ]);
        } else {
            $client = new SqsClient([
                "profile" => "defualt",
                "region" => $region,
                "version" => "2012-11-05",
            ]);
        }

        $queue_url = Config::get("admin.mail.http.queue_url");
        if (empty($queue_url)) {
            $this->w->Log->error("Failed to send mail to: $to, from: $reply_to, about: $subject: admin.mail.http.queue_url not set in config");
            return;
        }

        if (empty($to) || strlen($to) === 0) {
            $this->w->Log->error("Failed to send mail to: $to, from: $reply_to, about: $subject: no recipients");
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

        if (strpos($reply_to, ",") !== false) {
            $reply_to = array_map("trim", explode(",", $reply_to));
        }

        if (empty($cc)) {
            $cc = [];
        }

        if (empty($bcc)) {
            $bcc = [];
        }

        if (empty($reply_to)) {
            $reply_to = [];
        }

        $body_content_type = null;

        foreach ($headers as $key => $value) {
            if ($key === "Content-Type") {
                $body_content_type = $value;
                unset($headers[$key]);
            }
        }

        $attachmentsWithTypes = [];

        // Assume all types are S3. Currently HttpTransport requires attachments
        // to be in S3 to avoid breaking changes to this method's API.
        foreach ($attachments as $attachment) {
            $attachmentsWithTypes[] = [
                "path" => Config::get("file.adapters.s3.bucket") . "/" . $attachment,
                "type" => "s3",
            ];
        }

        $message_body = [
            "to" => is_array($to) ? $to : [$to],
            "cc" => is_array($cc) ? $cc : [$cc],
            "bcc" => is_array($bcc) ? $bcc : [$bcc],
            "reply_to" => is_array($reply_to) ? $reply_to : [$reply_to],
            "from" => is_array($reply_to) && count($reply_to) > 1 ? $reply_to[0] : $reply_to,
            "subject" => $subject,
            "body" => $body,
            "body_content_type" => $body_content_type,
            "headers" => $headers,
            "attachments" => $attachmentsWithTypes,
        ];

        $client->sendMessage([
            "QueueUrl" => $queue_url,
            "MessageBody" => json_encode($message_body),
        ]);
    }
}
