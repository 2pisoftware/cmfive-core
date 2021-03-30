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

// var_dump($rows);
// die;
    InsightService::getInstance($w)->exportcsv($run_data, $p['insight_class']);
}
