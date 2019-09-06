<?php

function duplicate_ALL(Web $w)
{
    $redirect = "/report/index";
    list($report_id) = $w->pathMatch("id");

    if (empty($report_id)) {
        $w->error("Failed to find Report to duplicate", $redirect);
    }

    $db_report = $w->Report->getReport($report_id);
    if (empty($db_report)) {
        $w->error("Failed to find Report to duplicate", $redirect);
    }

    $duplicate_report = new Report($w);
    $duplicate_report->title = $db_report->title;
    $duplicate_report->module = $db_report->module;
    $duplicate_report->category = $db_report->category;
    $duplicate_report->description = $db_report->description;
    $duplicate_report->report_code = $db_report->report_code;
    $duplicate_report->sqltype = $db_report->sqltype;
    $duplicate_report->report_connection_id = intval($w->request("report_connection_id"));

    if (!$duplicate_report->insert()) {
        $w->error("Failed to save duplicated Report", $redirect);
    }

    $report_member = new ReportMember($w);
    $report_member->report_id = $duplicate_report->id;
    $report_member->user_id = $w->Auth->user()->id;
    $report_member->role = "OWNER";

    if (!$report_member->insert()) {
        $w->error("Failed to save duplicated Report Member", $redirect);
    }

    $w->msg("Successfully duplicated Report", $redirect);
}
