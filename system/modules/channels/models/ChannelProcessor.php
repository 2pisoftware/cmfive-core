<?php

class ChannelProcessor extends DbObject
{

    public $name;
    public $class;
    public $module;
    public $processor_settings;
    public $settings; // Was filter_settings, not sure why
    public $channel_id;

    public function getChannel()
    {
        return ChannelService::getInstance($this->w)->getChannel($this->channel_id);
    }

    public function retrieveProcessor()
    {
        try {
            $processor = new $this->class($this->w);
            return $processor;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getUnprocessedMessages()
    {
        return $this->getObjects('ChannelMessage', ['channel_id' => $this->channel_id, 'is_processed' => 0, 'is_deleted' => 0]);
    }

    /**
     * Gets new messages that have not been processed at all
     * @return array new message objects
     */
    public function getNewMessages()
    {
        $messages = $this->getUnprocessedMessages();
        if (!empty($messages)) {
            $processor_id = $this->id;
            return array_filter($messages, function ($message) use ($processor_id) {
                $status = $message->getStatus($processor_id);
                return empty($status->id);
            });
        }
        return [];
    }

    public function getFailedMessages()
    {
        $messages = $this->getUnprocessedMessages();

        $processor_id = $this->id;
        return array_filter($messages ?: [], function ($message) use ($processor_id) {
            $status = $message->getStatus($processor_id);

            return !empty($status->id) && $status->is_successful != 1;
        });
    }

    public function getNewOrFailedMessages()
    {
        $messages = $this->getUnprocessedMessages();

        $processor_id = $this->id;
        return array_filter($messages ?: [], function ($message) use ($processor_id) {
            $status = $message->getStatus($processor_id);

            return empty($status->id) || (!empty($status->id) && $status->is_successful != 1);
        });
    }
}
