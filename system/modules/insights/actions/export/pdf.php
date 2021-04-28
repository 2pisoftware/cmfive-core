<?php
function pdf_ALL(Web $w)
{
    //Drop-down for chossing template to use for export
    $chooseTemplate = TemplateService::getInstance($w)->findTemplates(null,null,false,false);
    
    //Find class name of insight
    $p = $w->pathMatch('insight_class');
    //Find insight that matches class name
    $insight = InsightService::getInstance($w)->getInsightInstance($p['insight_class']);

    //put data for insight in template

    $run_data = $insight->run($w, $_REQUEST);
    //create service funtion for export to PDF to use here
}