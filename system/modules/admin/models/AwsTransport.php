<?php

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

        $region = Config::get("admin.mail.aws.region");
        if (empty($region)) {
            LogService::getInstance($this->w)->error("Failed to send mail to: admin.mail.aws.region not set in config");
            return;
        }

        $args = [
            "region" => $region,
            "version" => "2012-11-05",
        ];

        if (Config::get("system.environment", ENVIRONMENT_PRODUCTION) === ENVIRONMENT_DEVELOPMENT) {
            $credentials = Config::get("admin.mail.aws.credentials");
            if (empty($credentials)) {
                LogService::getInstance($this->w)->error("Failed to send mail to: admin.mail.aws.credentials not set in config");
                return;
            }

            $args["credentials"] = [
                "key" => $credentials["key"],
                "secret" => $credentials["secret"],
            ];
        }

        return new SqsClient($args);
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
        $client = $this->getTransport(null);

        $queue_url = Config::get("admin.mail.aws.queue_url");
        if (empty($queue_url)) {
            LogService::getInstance($this->w)->error("Failed to send mail to: $to, from: $reply_to, about: $subject: admin.mail.aws.queue_url not set in config");
            return;
        }

        $from_arn = Config::get("admin.mail.aws.from_arn");
        if (empty($from_arn)) {
            LogService::getInstance($this->w)->error("Failed to send mail to: $to, from: $reply_to, about: $subject: admin.mail.aws.from_arn not set in config");
            return;
        }

        if (empty($to) || strlen($to) === 0) {
            LogService::getInstance($this->w)->error("Failed to send mail to: $to, from: $reply_to, about: $subject: no recipients");
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

        // Assume all types are S3. Currently AwsTransport requires attachments
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
            "from_arn" => $from_arn,
        ];

        $client->sendMessage([
            "QueueUrl" => $queue_url,
            "MessageBody" => json_encode($message_body),
        ]);
    }
}
