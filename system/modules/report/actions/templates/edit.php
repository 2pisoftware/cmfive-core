<?php

function edit_GET(Web $w) {
    $p = $w->pathMatch("report_id", "id");
    $report_template = !empty($p['id']) ? $w->Report->getReportTemplate($p['id']) : new ReportTemplate($w);
    
    $form = array(
		__("Add Report Template") => array(
			array(
				array(__("Template"), "select", "template_id", $report_template->template_id, $w->Template->findTemplates("report"))
			),
			array(
				array(__("Type"), "select", "type", $report_template->type, $report_template->getReportTypes())
			),
			array(
				array(__("Use in emailed reports"), "checkbox", "is_email_template", $report_template->is_email_template)
			),
			array(
				array(__("Report ID"), "hidden", "report_id", $p['report_id'])
			)
		)  
    );
    
    $w->out(Html::multiColForm($form, "/report-templates/edit/{$report_template->id}"));
}

function edit_POST(Web $w) {
    $p = $w->pathMatch("id");
    $report_template = !empty($p['id']) ? $w->Report->getReportTemplate($p['id']) : new ReportTemplate($w);
    
    $report_template->fill($_POST);
	$report_template->is_email_template = intval(!empty($_POST['is_email_template']));
    $response = $report_template->insertOrUpdate();
    
    $w->msg(__("Report template ") . (!empty($p['id']) ? __("updated") : __("created")), "/report/edit/{$report_template->report_id}#templates");
}
