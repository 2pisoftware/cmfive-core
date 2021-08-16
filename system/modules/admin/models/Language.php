<?php

class Language extends DbObject
{
    public $name;
    public $native_name;
    public $iso_639_1;
    public $iso_639_2;

    public function getSelectOptionTitle()
    {
        return $this->name;
    }

    public function getSelectOptionValue()
    {
        return $this->iso_639_2;
    }
}
