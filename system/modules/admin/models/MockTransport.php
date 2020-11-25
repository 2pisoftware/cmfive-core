<?php

/**
 * MockTransport is a the transport used in tests.
 */
class MockTransport implements GenericTransport
{
    private $w;

    public function __construct($w, $layer)
    {
        $this->w = $w;
    }

    public function getTransport($layer)
    {
    }

    public function send($to, $replyto, $subject, $body, $cc = null, $bcc = null, $attachments = [], $headers = [])
    {
        LogService::getInstance($this->w)->setLogger("ACCEPTANCE_TEST")->info("MockTransport::send() called");
    }
}
