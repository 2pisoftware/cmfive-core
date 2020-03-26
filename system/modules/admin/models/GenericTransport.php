<?php

interface GenericTransport
{
    public function __construct($w, $layer);
    public function getTransport($layer);
    public function send($to, $replyto, $subject, $body, $cc = null, $bcc = null, $attachments = [], $headers = []);
}
