<?php

class Migration extends DbObject {

    public $path;
    public $classname;
    public $module;
    public $dt_created;
    public $batch;
    public $pretext;
    public $posttext;
    public $description;

    /**
     * A static array of string arrays to be used for validaiton when creating forms with a Migration in it.
     *
     * @var array[array[string]]
     */
    public static $_validation = [
        'name' => ['required']
    ];
}