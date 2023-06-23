<?php

use Html\Form\Select;

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

    //Drop-down for choosing template to use for export
    $templates = TemplateService::getInstance($w)->findTemplates('insights', $insight_class_name_pdf, false, false);
    $template_select = HtmlBootstrap5::multiColForm(
        [
            'Select template for PDF layout (optional)' => [
                [
                    (new Select([
                        'id|name' => 'template_id',
                        'selected_option' => $requestedTemplate,
                        'options' => $templates,
                    ])),
                ]
            ],
            'Select Page Layout' => [
                [
                    [HtmlBootstrap5::radio("layout_P", "layout_selection", "P", "P") . " : Portrait"],
                    [HtmlBootstrap5::radio("layout_L", "layout_selection", null, "L") . " : Landscape"],
                ]
            ]
        ],
        "/insights-export/pdf/" . $p['insight_class'] . "?" . $refreshedParameters,
        "POST",
        "Export",
        "template_select_form",
        null, null,
        "_self",
        true, null, $displayOverlay = false
    );

    $w->out($template_select);
}