<?php

class AuditInsight extends InsightBaseClass {

    public $name = "Audit";
    public $module = "Main";
    public $description = "Lists audit log records";

    public function getFilters(Web $w, array $params = []): array {
        return null;
    }

    public function run(Web $w, array $params = []): array {
        return null;
    }



}