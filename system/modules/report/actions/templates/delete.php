<?php

function delete_GET(Web $w) {
    $p = $w->pathMatch("id");
    if (empty($p['id'])) {
        $w->error(__("Report template not found"), "/report-templates");
    }
    
    $report_template = $w->Report->getReportTemplate($p['id']);
    if (empty($report_template->id)) {
        $w->error(__("Report template not found"), "/report-templates");
    }
    
    $report_template->delete();
    $w->msg(__("Report template removed"), "/reports/edit/{$report_template->report_id}#templates");
}
