<?php

function edit_GET(Web $w) {
    $p = $w->pathMatch("report_id", "id");
    $report_template = !empty($p['id']) ? ReportService::getInstance($w)->getReportTemplate($p['id']) : new ReportTemplate($w);
    
    $form = array(
        "Add Report Template" => array(
            array(
                array("Template", "select", "template_id", $report_template->template_id, TemplateService::getInstance($w)->findTemplates("report"))
            ),
            array(
                array("Type", "select", "type", $report_template->type, $report_template->getReportTypes())
            ),
            array(
                array("Use in emailed reports", "checkbox", "is_email_template", $report_template->is_email_template)
            ),
            array(
                array("Report ID", "hidden", "report_id", $p['report_id'])
            )
        )  
    );
    
    $w->out(HtmlBootstrap5::multiColForm($form, "/report-templates/edit/{$report_template->id}"));
}

function edit_POST(Web $w) {
    $p = $w->pathMatch("id");
    $report_template = !empty($p['id']) ? ReportService::getInstance($w)->getReportTemplate($p['id']) : new ReportTemplate($w);
    
    $report_template->fill($_POST);
    $report_template->is_email_template = intval(!empty($_POST['is_email_template']));
    $response = $report_template->insertOrUpdate();
    
    $w->msg("Report template " . (!empty($p['id']) ? "updated" : "created"), "/report/edit/{$report_template->report_id}#templates");
}