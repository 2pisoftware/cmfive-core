<?php

class ChannelMessage extends DbObject
{
    public $dt_created;
    public $channel_id;
    public $message_type;

    public $__use_auditing = false;

    // public $is_processed;

    public function getChannel()
    {
        return ChannelService::getInstance($this->w)->getChannel($this->channel_id);
    }

    public function getData()
    {
        $attachments = FileService::getInstance($this->w)->getAttachments($this, $this->id);
        if (!empty($attachments)) {
            foreach ($attachments as $attachment) {
                // return the serialised email object
                if ($attachment->filename == "email.txt") {
                    return $attachment->getContent();
                }
            }
            return $attachments[0]->getContent();
        }
        return null;
    }

    public function getStatus($processor_id)
    {
        return ChannelService::getInstance($this->w)->getMessageStatus($this->id, $processor_id);
    }

    public function getFailedProcesses()
    {
        $resultset = $this->w->db->get("channel_message_status")
            ->where("message_id", $this->id)
            ->where("is_successful", 0);
        if (!empty($resultset)) {
            return $resultset->count();
        }
        return 0;
    }
}
