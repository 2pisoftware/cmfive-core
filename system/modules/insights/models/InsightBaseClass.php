<?php
/**@author Alice Hutley <alice@2pisoftware.com> */

abstract class InsightBaseClass

{
    public $name;
    public $module;
    public $description;

    abstract public function getFilters(Web $w): array;

    abstract public function run(Web $w, array $params = []): array;

    public function getMembers(Web $w){
        return InsightService::getInstance($w)->getAllMembersForInsightClass(get_class($this));
    }

}
