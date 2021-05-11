<?php
function pdf_ALL(Web $w)//GET
{
    //Find class name of insight
    $p = $w->pathMatch('insight_class');
    //Find insight that matches class name
    $insight = InsightService::getInstance($w)->getInsightInstance($p['insight_class']);
    $insight_class_name_pdf = $p['insight_class'] . '_pdf';
        
    //Drop-down for chossing template to use for export
    $templates = TemplateService::getInstance($w)->findTemplates('insights',$insight_class_name_pdf,false,false);
    //var_dump($templates); die;
    $template_list = array(
        array("", "hidden", "insight_class", $p['insight_class'])
    );
    $template_list[] =  array("Template", "select", "template_id", null, $templates);

}
//POST
//put data for insight in template
//$run_data = $insight->run($w, $_REQUEST);
    
//create service funtion for export to PDF to use here