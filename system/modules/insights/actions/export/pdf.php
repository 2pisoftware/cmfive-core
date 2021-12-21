<?php

function pdf_ALL(Web $w)
{
    $w->setLayout(null);
    //Find class name of insight
    $p = $w->pathMatch('insight_class');
    if (empty($p['insight_class'])) {
        $w->error('No insight class name found', '/insights');
    }
    //find insight that matches class name
    $insight = InsightService::getInstance($w)->getInsightInstance($p['insight_class']);
    if (empty($insight)) {
        $w->error('No insight found for class name', '/insights');
    }
    $run_data = $insight->run($w, $_REQUEST);

    //create service funtion for export to PDF to use here
    InsightService::getInstance($w)
        ->exportpdf(
            $run_data,
            $insight->name,
            $_REQUEST['template_id'] ?? null,
            $_REQUEST['layout_selection'] ?? "P"
        );
}
