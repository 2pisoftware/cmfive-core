<?php

class ChannelMessageStatus extends DbObject
{
    public $message_id;
    public $processor_id;
    public $message;
    public $is_successful;

    public $__use_auditing = false;
}
