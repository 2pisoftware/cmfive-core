<?php

class EmailStructure
{
    public $to;
    public $cc;
    public $from;
    public $from_email_address;
    public $message_id;
    public $subject;
    public $body = ["plain" => "", "html" => ""];

    // Maybe
    public $attachments = [];
}
