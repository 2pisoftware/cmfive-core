<?php
function pdf_ALL(Web $w)
{
    //Find class name of insight
    $p = $w->pathMatch('insight_class');
    //Find insight that matches class name
    $insight = InsightService::getInstance($w)->getInsightInstance($p['insight_class']);
    $insight_class_name = $insight->name;
        
    //Drop-down for chossing template to use for export
    $templates = TemplateService::getInstance($w)->findTemplates(null,null,false,false);
    $choose_template = [];
        foreach ($templates as $temp_name) {
            $row = [];
                if(strpos($temp_name->title, $insight_class_name) !== false){
                    $row = $temp_name->title;
                }; 
                $choose_template = $row;
        };
    $template_list = array(
        array("", "hidden", "insight_class_name", $insight_class_name)
    );
    $template_list[] =  array("Template", "select", "title", null, $choose_template);

    //put data for insight in template
    $run_data = $insight->run($w, $_REQUEST);
    
    //create service funtion for export to PDF to use here
}