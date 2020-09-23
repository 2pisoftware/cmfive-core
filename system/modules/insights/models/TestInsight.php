
<?php

class TestInsight extends InsightBaseClass
{
    public $name = "Test Insight";
    public $description = "Test model for insights";

    public function getFilters(Web $w): array
    {
    }

    public function run(Web $w, array $params = []): array
    {
    }
}
