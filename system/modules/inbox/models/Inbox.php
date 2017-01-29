<?php

class Inbox extends DbObject {

    public $subject;
    public $user_id;
    public $parent_message_id;
    public $message_id;
    public $dt_created;
    public $dt_read;
    public $is_new;
    public $dt_archived;
    public $is_archived;
    public $has_parent;
    public $sender_id;
    public $del_forever;
    public $_message;

    function getMessage() {
        if ($this->message_id !== null && !$this->_message) {
            $msg = $this->getObject("Inbox_message", $this->message_id);
            if ($msg) {
                $this->_message = $msg->message;
            }
        }
        return $this->_message;
    }

    function getSender() {
        if (null !== $this->sender_id) {
            return $this->Auth->getUser($this->sender_id);
        } else {
            return null;
        }
    }

    function getParentMessage() {
        if (!$this->parent_message_id == 0) {
            $message = $this->getMessage($this->parent_message_id);
            $message_arr[$this->parent_message_id] = $message;
            $message->getParentMessage();
        }
        return $message;
    }

}
