<?php

/**
 * @author Alice Hutley <alice@2pisoftware.com>
 */
function runInsight_GET(Web $w)
{
    $w->setLayout('layout-bootstrap-5');
    
    $p = $w->pathMatch('insight_class');
    if (empty($p['insight_class'])) {
        $w->error('No insight class found', '/insights');
    }
    /** @var InsightBaseClass $insight */
    $insight = InsightService::getInstance($w)->getInsightInstance($p['insight_class']);
    if (empty($insight)) {
        $w->error('Insight class could not resolve', '/insights');
    }

    $w->ctx('insight_class_name', $p['insight_class']);
    $w->ctx('insight', $insight);
    $w->ctx('title', $insight->name);
    $run_data = $insight->run($w, $_GET);
    /** @var InsightReportInterface $data */
    /**Defines how the insight table should look. Gives column heading for the data defined in each insight class */
    $w->ctx('run_data', $run_data);

    //build request string for editing parameters
    $w->ctx('request_string', http_build_query($_GET));

    //template_select
    $insight_class_name_pdf = $p['insight_class'] . '_pdf';

    //Drop-down for chossing template to use for export
    $templates = TemplateService::getInstance($w)->findTemplates('insights', $insight_class_name_pdf, false, false);

    $template_select = (new \Html\Form\Select())->setLabel('Template')->setName('template_id')->setOptions($templates, true);//->setRequired(false)

    $w->ctx('template_select', $template_select);

}
