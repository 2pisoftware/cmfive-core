<?php

class TestProcessor extends ProcessorType
{
    public function getSettingsForm($current_settings = null)
    {
        // Check if json
        if (!empty($current_settings)) {
            if (is_string($current_settings)) {
                $current_settings = json_decode($current_settings);
            }
        }

        return [
            "Settings" => [
                [
                    ["My Setting", "text", "my_setting", @$current_settings->my_setting]
                ]
            ]
        ];
    }

    public function process($processor)
    {
        if (empty($processor->id)) {
            return;
        }

        $messages = ChannelService::getInstance($processor->w)->getMessages($processor->channel_id);
        if (!empty($messages)) {
            foreach ($messages as $message) {
                if ($message->is_processed == 0) {
                    $rawdata = $message->getData();
                    if (!empty($rawdata)) {
                        $emailmessage = new EmailMessage($rawdata);
                        $email = $emailmessage->parse();

                        echo $email->getBodyText();
                        $message->is_processed = 1;
                        $message->update();
                    }
                }
            }
        }
    }
}
