<?php

function duplicate_ALL(Web $w)
{
    $failure_redirect_url = "/report/index";
    list($report_id) = $w->pathMatch("id");

    if (empty($report_id)) {
        $w->error("Failed to find Report to duplicate", $failure_redirect_url);
    }

    $db_report = ReportService::getInstance($w)->getReport($report_id);
    if (empty($db_report)) {
        $w->error("Failed to find Report to duplicate", $failure_redirect_url);
    }

    $duplicate_report = $db_report->copy();
    $duplicate_report->title .= " - Copy";
    $duplicate_report->report_connection_id = intval(Request::int("report_connection_id"));

    if (!$duplicate_report->insert()) {
        $w->error("Failed to save duplicated Report", $failure_redirect_url);
    }

    $db_members = $db_report->getMembers();

    foreach ($db_members ?? [] as $db_member) {
        $duplicate_member = $db_member->copy();
        $duplicate_member->report_id = $duplicate_report->id;

        if (!$duplicate_member->insert()) {
            LogService::getInstance($w)->setLogger("REPORT")->error("Failed to insert ReportMember when duplicating Report");
        }
    }

    $w->msg("Successfully duplicated Report", "/report/edit/{$duplicate_report->id}");
}
