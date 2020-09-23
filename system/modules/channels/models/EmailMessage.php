<?php

/**
 * Fascade Message class to parse raw email messages for a processor
 */
class EmailMessage extends DbService
{

    private $_rawdata;

    public function __construct($rawdata)
    {
        $this->_rawdata = $rawdata;
        return $this;
    }

    public function parse()
    {
        return unserialize($this->_rawdata);
    }
}
