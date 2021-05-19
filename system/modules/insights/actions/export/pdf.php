<?php
function pdf_GET(Web $w)
{
    //var_dump($_GET); die;
    //Find class name of insight
    $p = $w->pathMatch('insight_class');
    //Find insight that matches class name
    //$insight = InsightService::getInstance($w)->getInsightInstance($p['insight_class']);
    $insight_class_name_pdf = $p['insight_class'] . '_pdf';
        
    //Drop-down for chossing template to use for export
    $templates = TemplateService::getInstance($w)->findTemplates('insights',$insight_class_name_pdf,false,false);
    //var_dump($insight_name); die;
    //build form for drop-down
    $template_list = array( "template"=>[
        
        [
            array("", "hidden", "insight_class", $p['insight_class']),
            array("Template", "select", "template_id", null, $templates)
        ]
        
    ]
    );
   

    //Send template to the post method
    $postUrl = '/insights-export/pdf?' . http_build_query($_GET);

    $w->out(Html::multiColForm($template_list, $postUrl));

}

function pdf_POST(Web $w)
{
    //var_dump($_REQUEST); die;
    //retrieve data for insight
    $insight = InsightService::getInstance($w)->getInsightInstance($_POST['insight_class']);
    $run_data = $insight->run($w, $_REQUEST);
    $data_array = json_decode(json_encode($run_data),true);
    //var_dump($data_array); die;
    //retieve slected template from GET function

    //use template service render function to place $run_data and $insight_name in template
    TemplateService::getInstance($w)->render($_POST['template_id'], $data_array);

    //create service funtion for export to PDF to use here
    InsightService::getInstance($w)->exportpdf($data_array, $insight->name, null);

    //redirect/close pop-up box
    //$w->redirect('/insights/runInsight/' . $_POST['insight_class']);
}