<?php
class Audit extends DbObject
{
    public $dt_created;
    public $creator_id;
    public $module;
    public $submodule;
    public $action;
    public $path;
    public $ip;
    public $db_action;
    public $db_class;
    public $db_id;
    public $message;

    public $__use_auditing = false;
}
