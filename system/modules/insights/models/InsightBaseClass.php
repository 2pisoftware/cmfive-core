<?php

abstract class InsightBaseClass

{
    public $name;
    public $module;
    public $description;

    abstract public function getFilters(Web $w): array;

    abstract public function run(Web $w, array $params = []): array;
}
?>