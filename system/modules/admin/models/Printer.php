<?php

class Printer extends DbObject {

    public $name;
    public $server;
    public $port;

    /**
     * A static array of string arrays to be used for validaiton when creating forms with a Printer in it.
     *
     * @var array[array[string]]
     */
    public static $_validation = [
        'name' => ['required']];
}
