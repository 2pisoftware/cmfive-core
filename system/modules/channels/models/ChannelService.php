<?php

class ChannelService extends DbService
{
    /**
     * Returns all non-deleted channel objects
     * @return Array<Channel> channels
     */
    public function getChannels()
    {
        $where = ["is_deleted" => 0];
        return $this->getObjects("Channel", $where);
    }

    /**
     * Returns a non-deleted channel object
     * @return Object channel
     */
    public function getChannel($id)
    {
        return $this->getObject("Channel", $id);
    }

    /**
     * Returns a non-deleted email channel object
     * @return Object emailchannel
     */
    public function getEmailChannel($channel_id)
    {
        $where = ['is_deleted' => 0, "channel_id" => $channel_id];
        return $this->getObject('EmailChannelOption', $where);
    }

    /**
     * Returns a non-deleted email channel object
     * @return Object emailchannel
     */
    public function getWebChannel($channel_id)
    {
        $where = ['is_deleted' => 0, "channel_id" => $channel_id];
        return $this->getObject('WebChannelOption', $where);
    }

    /**
     * Returns all non-deleted email channel objects
     * @return Array<EmailChannelOption> emailchannels
     */
    public function getEmailChannels()
    {
        $where = ['is_deleted' => 0];
        return $this->getObjects('EmailChannelOption', $where);
    }

    /**
     * Returns all non-deleted email channel objects
     * @return Array<EmailChannelOption> emailchannels
     */
    public function getWebChannels()
    {
        $where = ['is_deleted' => 0];
        return $this->getObjects('WebChannelOption', $where);
    }

    /**
     * Returns all non-deleted email channel objects
     * @return Array<EmailChannelOption> emailchannels
     */
    public function getChildChannel($id)
    {
        $child_channel = $this->getEmailChannel($id);
        if (!isset($child_channel)) {
            $child_channel = $this->getWebChannel($id);
        }

        return $child_channel;
    }

    /**
     * Returns all non-deleted processor objects
     * @return Array<ChannelProcessor> processors
     */
    public function getProcessors($channel_id = null)
    {
        if (empty($channel_id)) {
            return null;
        }
        $where = ["is_deleted" => 0, "channel_id" => $channel_id];
        return $this->getObjects("ChannelProcessor", $where);
    }

    public function getAllProcessors()
    {
        return $this->getObjects("ChannelProcessor", ["is_deleted" => 0]);
    }

    /**
     * Returns a non-deleted processor object
     * @return Object processor
     */
    public function getProcessor($id)
    {
        $where = ["is_deleted" => 0, "id" => $id];
        return $this->getObject("ChannelProcessor", $where);
    }

    /**
     * Returns a parsed list of available processors
     * @return Array list
     */
    public function getProcessorList()
    {
        // Get Modules => Processor list
        $list = [];
        foreach ($this->w->modules() as $module) {
            $processors = Config::get("{$module}.processors");
            if (!empty($processors)) {
                foreach ($processors as $processor) {
                    $list[] = $module . "." . $processor;
                }
            }
        }

        return $list;
    }

    public function getMessages($channel_id = null, $include_deleted = false)
    {
        $where = [];
        if ($include_deleted === false) {
            $where["is_deleted"] = 0;
        }
        if (!empty($channel_id)) {
            $where["channel_id"] = $channel_id;
        }

        return $this->getObjects("ChannelMessage", $where, false, true, "dt_created desc");
    }

    public function getMessage($id)
    {
        return $this->getObject("ChannelMessage", $id);
    }

    public function getMessageStatus($message_id, $processor_id = null)
    {
        $where = ["message_id" => $message_id];
        if (!empty($processor_id)) {
            $where["processor_id"] = $processor_id;
        }

        return $this->getObject("ChannelMessageStatus", $where);
    }

    public function getMessageStatuses($message_id)
    {
        $where = ["message_id" => $message_id];
        return $this->getObjects("ChannelMessageStatus", $where);
    }

    /**
     * DEPRECATED: This function doesn't work as intended as it disregards both the
     * $channel_id and $processor_id given
     *
     * Use the ChannelProcessor::getNewMessages() / getFailedMessages() / getNewOrFailedMessages() instead
     *
     * @param  Mixed $channel_id Channel ID
     * @param  Mixed $processor_id Processor ID
     * @return Array<ChannelMessage>
     */
    public function getNewOrFailedMessages($channel_id, $processor_id)
    {
        // Get list of failed messages
        $failed_messages = $this->_db->get("channel_message")
            ->leftJoin("channel_message_status on channel_message.id = channel_message_status.message_id")
            ->where("channel_message_status.is_successful", 0)->fetchAll();
        if (!empty($failed_messages)) {
            foreach ($failed_messages as $fm) {
                $failed_ids[] = $fm['id'];
            }
        }

        // Get the message statuses
        if (!empty($failed_ids)) {
            $message_statuses = $this->_db->get("channel_message_status")->where("message_id", $failed_ids)->fetchAll();
            $message_statuses_objects = $this->fillObjects("ChannelMessageStatus", $message_statuses);
        }

        // Fill objects accordingly
        $failed_message_objects = [];
        if (!empty($failed_messages)) {
            $failed_message_objects = $this->fillObjects("ChannelMessage", $failed_messages);
            foreach ($failed_message_objects as &$fmo) {
                // Try and find the matching status
                foreach ($message_statuses_objects as $mso) {
                    if ($mso->message_id == $fmo->id) {
                        $fmo->messagestatus = $mso;
                        break;
                    }
                }
            }
        }

        // Get new messages
        $new_messages = $this->_db->sql("select channel_message.* from channel_message left join channel_message_status on channel_message.id = channel_message_status.message_id where channel_message_status.id IS NULL")->fetchAll();
        $new_message_objects = [];
        if (!empty($new_messages)) {
            $new_message_objects = $this->fillObjects("ChannelMessage", $new_messages);
        }

        return (array_merge($new_message_objects, $failed_message_objects));
    }

    public function markMessagesAsProcessed($channel_id)
    {
        $this->w->db->update('channel_message', ['is_processed' => 1])->where('channel_id', $channel_id)
            ->where('is_deleted', 0)->execute();
    }

    /**
     * Channels naivgation function
     * @return none
     */
    public function navigation(Web $w, $title = null, $prenav = null)
    {
        if ($title) {
            $w->ctx("title", $title);
        }
        $nav = $prenav ? $prenav : [];
        if (AuthService::getInstance($w)->loggedIn()) {
            $w->menuLink("channels/listchannels", "List Channels", $nav);
            $w->menuLink("channels/listprocessors", "List Processors", $nav);
            $w->menuLink("channels/listmessages", "List Messages", $nav);
        }
        $w->ctx("navigation", $nav);
        return $nav;
    }

    public function navList(): array
    {
        return [
            new MenuLinkStruct("List Channels", "channels/listchannels"),
            new MenuLinkStruct("List Processors", "channels/listprocessors"),
            new MenuLinkStruct("List Messages", "channels/listmessages"),
        ];
    }
}
