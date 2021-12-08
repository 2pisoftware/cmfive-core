<?php

use Aws\Sqs\SqsClient;
use Aws\Exception\AwsException;

class AntivirusService extends DbService
{
    public function processQueue()
    {
        $queue_url = Config::get('file.av_queue_url');

        if (empty($queue_url)) {
            return;
        }

        $client = new SqsClient([
            'profile' => 'default',
            'region' => 'ap-southeast-2',
            'version' => '2012-11-05'
        ]);
        
        try {
            $result = $client->receiveMessage([
                'AttributeNames' => ['SentTimestamp'],
                'MaxNumberOfMessages' => 10,
                'MessageAttributeNames' => ['All'],
                'QueueUrl' => $queue_url, // REQUIRED
                'WaitTimeSeconds' => 5,
            ]);
            if (!empty($result->get('Messages'))) {
                foreach ($result->get('Messages') as $message) {
                    $body = json_decode($message['Body']);

                    if (array_key_exists("responsePayload", $body)) {
                        $scan_details = $body["responsePayload"];
                        if (array_key_exists("status", $scan_details)) {
                            if (strtoupper($scan_details['status']) == "INFECTED") {
                                /** @var Attachment */
                                $attachment = $this->getObject('Attachment', ['fullpath' => $scan_details['input_key']]);
                                LogService::getInstance($this->w)->error("Removing infected file: " . $attachment->filename);
                                if (!empty($attachment)) {
                                    $attachment->delete();
                                } else {
                                    LogService::getInstance($this->w)->error("Infected file found with no matching attachment object");
                                }
                            }
                        }
                    }

                    $result = $client->deleteMessage([
                        'QueueUrl' => $queue_url,
                        'ReceiptHandle' => $message['ReceiptHandle']
                    ]);
                }
            }
        } catch (AwsException $e) {
            // output error message if fails
            LogService::getInstance($this->w)->error('Error with processing queue: ' . $e->getMessage());
        }
    }
}