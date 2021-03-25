<?php
class Lookup extends DbObject
{
    public $weight;
    public $type;
    public $code;
    public $title;
    public $is_deleted;

    public function getDbTableName()
    {
        return "lookup";
    }

    public function getSelectOptionValue()
    {
        return $this->code;
    }

    public function getSelectOptionTitle()
    {
        return $this->title;
    }
}
