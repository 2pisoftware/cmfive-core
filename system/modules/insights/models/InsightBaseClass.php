<?php

abstract class InsightBaseClass
{
    public $name;
    public $module;
    public $description;

    abstract public function getFilters();

    abstract public function run();
}
