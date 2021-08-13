<?php

function pdf_POST(Web $w)
{
    //Find class name of insight
    $p = $w->pathMatch('insight_class');

    //retrieve data for insight
    $insight = InsightService::getInstance($w)->getInsightInstance($p['insight_class']);
    $run_data = $insight->run($w, $_REQUEST);

    //create service funtion for export to PDF to use here
    InsightService::getInstance($w)->exportpdf($run_data, $insight->name, $_POST['template_id']);
}
