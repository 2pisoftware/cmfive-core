<?php

class Channel extends DbObject
{
    public $name;
    public $is_active;          // 0|1 flag
    public $notify_user_email;  // not in use
    public $notify_user_id;     // not in use
    public $do_processing;      // 0|1 flag

    public function getForm()
    {
        return [
            "Channel" => [
                [
                    ["Name", "text", "name", $this->name],
                    ["Is Active", "checkbox", "is_active", empty($this->name) ? 1 : $this->is_active]
                ],
                [
                    ["Run processors?", "checkbox", "do_processing", empty($this->name) ? 1 : $this->do_processing]
                ]
            ]
        ];
    }

    public function read($markAsProcessed = true)
    {
        $channelImpl = $this->Channel->getChildChannel($this->id);
        if (!empty($channelImpl)) {
            $channelImpl->read();
        }
        if ($this->do_processing) {
            // Run processors
            $processors = $this->w->Channel->getProcessors($this->id);
            if (!empty($processors)) {
                foreach ($processors as $processor) {
                    $processor_class = $processor->retrieveProcessor();
                    $processor_class->process($processor);
                }
                if ($markAsProcessed) {
                    $this->Channel->markMessagesAsProcessed($this->id);
                }
            }
        }
    }

    public function getNotifyUser()
    {
        return $this->Auth->getUser($this->notify_user_id);
    }
}
