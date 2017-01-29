<?php

/**
 * Fascade Message class to parse raw email messages for a processor
 */
class EmailMessage extends DbService {

    private $_rawdata;

    public function __construct($rawdata) {
        $this->_rawdata = $rawdata;
        return $this;
    }

    public function parse() {
        return unserialize($this->_rawdata); //Zend_Mail_Message::fromString($this->_rawdata);
        // Do we need to do anything? Maybe get out attachements?
//        return $email;
    }

}
