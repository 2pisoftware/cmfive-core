<?php

function bindpdf_GET(Web $w)
{
    // we have:
    // "/insights-export/bind/" . $insight_class_name . "?" . $request_string_with_report_settings

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

    $requestedTemplate = $_GET['template_id'] ?? null;
    $_GET['template_id'] = null;
    $refreshedParameters = http_build_query($_GET);

    // don't drop the report settings!
    $w->ctx('request_string', $refreshedParameters);

    //template_select
    $insight_class_name_pdf = $p['insight_class'] . '_pdf';

    //Drop-down for chossing template to use for export
    $templates = TemplateService::getInstance($w)->findTemplates('insights', $insight_class_name_pdf, false, false);
    $template_select = Html::form(
        [
            [
                "Select template for PDF layout (optional)",
                "select",
                "template_id",
                $requestedTemplate,
                $templates
            ]
        ],
        "/insights-export/pdf/" . $p['insight_class'] . "?" . $refreshedParameters,
        "POST",
        "",
        "template_select_form"
    );

    $w->ctx('template_select', $template_select);
}
