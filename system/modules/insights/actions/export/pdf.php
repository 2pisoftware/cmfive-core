<?php
function pdf_GET(Web $w)
{
    //Find class name of insight
    $p = $w->pathMatch('insight_class');
    //Find insight that matches class name
    $insight = InsightService::getInstance($w)->getInsightInstance($p['insight_class']);
    $insight_name = $insight->name;
    $insight_class_name_pdf = $p['insight_class'] . '_pdf';
        
    //Drop-down for chossing template to use for export
    $templates = TemplateService::getInstance($w)->findTemplates('insights',$insight_class_name_pdf,false,false);
    //var_dump($insight_name); die;
    //build form for drop-down
    $template_list = array(
        array("", "hidden", "insight_class", $p['insight_class'])
    );
    $template_list[] =  array("Template", "select", "template_id", null, $templates);

    //Send template to the post method
    $postUrl = '/insights-export/pdf' . $p['insight_class'] . "template_id" . '';

    $w->out(Html::multiColForm($template_list, $postUrl));

}

function pdf_POST(Web $w)
{
    //retrieve data for insight
    $run_data = $p['insight_class']->run($w, $_REQUEST);
    //retieve slected template from GET function

    //use template service render function to place $run_data and $insight_name in template
    TemplateService::getInstance($w)->render($template_list["template_id"], $run_data);

    //create service funtion for export to PDF to use here

    //redirect/close pop-up box
    $w->redirect('/insights/runInsight?insight_class=' . $insight);
}