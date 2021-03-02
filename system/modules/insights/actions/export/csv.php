<?php
function csv_ALL(Web $w)
{

    $p = $w->pathMatch('insight_class');
    if (empty($p['insight_class'])) {
        $w->error('No insight class name found','/insights');
    }
    $insight = InsightService::getInstance($w)->getInsightInstance($p['insight_class']);
    if (empty($insight)) {
        $w->error('No insight found for class name','/insights');
    }
    $run_data = $insight->run($w, $_REQUEST);

    $rows = [];
    foreach ($run_data as $table) {
        if (!empty($table)) {
            $rows[] = [$table->title];
            $rows[] = $table->header;
            foreach ($table->data as $result_row) {
                $rows[] = $result_row;
            }
        }
    }

    InsightService::getInstance($w)->exportcsv($rows, $p['insight_class']);
}
