
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
        $run_data = $insight->run($w, $_GET);
        /** @var InsightReportInterface $data */
        foreach ($run_data as $data) {
          $w->out('<h3>' . $data->title . "</h3>");
            $w->out(Html::table($data->data, null, "tablesorter", $data->header));
        }
    }
}
