<?php

class EmailStructure {
    
    public $to;
    public $cc;
    public $from;
    public $subject;
    public $body = array("plain" => "", "html" => "");
    
    // Maybe
    public $attachments = array();
    
}