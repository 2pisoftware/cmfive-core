<?php
function pdf_ALL(Web $w)
{
    //Find class name of insight
    $p = $w->pathMatch('insight_class');
    //Find insight that matches class name
    $insight = InsightService::getInstance($w)->getInsightInstance($p['insight_class']);
    $insight_class_name = $insight->name;
    $insight_class_name_pdf = $insight_class_name . ' PDF';
        
    //Drop-down for chossing template to use for export
    $templates = TemplateService::getInstance($w)->findTemplates(null,$insight_class_name_pdf,false,false);
    var_dump($templates); die;
    $category = 
    $template_list = array(
        array("", "hidden", "insight_class_name", $insight_class_name)
    );
    $template_list[] =  array("Template", "select", "title", null, $templates);

    //put data for insight in template
    $run_data = $insight->run($w, $_REQUEST);
    
    //create service funtion for export to PDF to use here
}