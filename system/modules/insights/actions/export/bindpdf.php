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
        //"/insights-export/bindpdf/",
        "POST",
        "Export",
        "template_select_form"
    );
/*    
    $template_select = Html::form(
        [
            ["Select template for PDF layout (optional)", "section"],
            [
                "",
                "select",
                "template_id",
                $requestedTemplate,
                $templates
            ],
            ["&nbsp", "section"],
            ["Select page layout", "section"],
            [Html::radio("layout_P", "layout_selection", "P", "P") . " : Portrait"],
            [Html::radio("layout_L", "layout_selection", null, "L") . " : Landscape"],

        ],
        "/insights-export/pdf/" . $p['insight_class'] . "?" . $refreshedParameters,
        "POST",
        "",
        "template_select_form"
    );
*/
    //$w->ctx('template_select', $template_select);
    $w->out($template_select);
}

// This is an attempt to move the functionality from pdf.php to this file to see if this will prevent the "waiting" message hanging.
//NB For some reason can't call this function from the bindpdf_Get function? Get "Page not Found"?
function edit_POST(Web $w): void
{
    $w->setLayout(null);
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
    $run_data = $insight->run($w, $_REQUEST);

    //create service funtion for export to PDF to use here
    InsightService::getInstance($w)
        ->exportpdf(
            $run_data,
            $insight->name,
            $_REQUEST['template_id'] ?? null,
            $_REQUEST['layout_selection'] ?? "P"
        );

    $w->msg('PDF Exported', '/insights/viewInsight/BridgePortalReportInsight');
}