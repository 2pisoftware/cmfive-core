<?php

/**@author Alice Hutley <alice@2pisoftware.com> */

function runInsight_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');

    list($insight_class) = $w->pathMatch('insight_class');
    if (empty($insight_class)) {
        $w->error('No insight class found', '/insights');
    }

    $insight = InsightService::getInstance($w)->getInsightInstance($insight_class);
    if (empty($insight)) {
        $w->error('Insight class could not resolve', '/insights');
    }

    $w->ctx('insight_class_name', $insight_class);
    $w->ctx('insight', $insight);
    $w->ctx('title', $insight->name);
    
    $run_data = $insight->run($w, $_GET);
    $w->ctx('run_data', $run_data);

    //build request string for editing parameters
    $w->ctx('request_string', http_build_query($_GET));

    //template_select
    $insight_class_name_pdf = $insight_class . '_pdf';

    //Drop-down for chossing template to use for export
    $templates = TemplateService::getInstance($w)->findTemplates('insights', $insight_class_name_pdf, false, false);

    $template_select = (new \Html\Form\Select())->setLabel('Template')->setName('template_id')->setOptions($templates, true);

    $w->ctx('template_select', $template_select);
}
