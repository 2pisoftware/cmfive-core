<?php

function index_ALL(Web &$w)
{
    ReportService::getInstance($w)->navigation($w, "Reports");
    History::add("List Reports");
    // report approval flag: display appropriate image
    $app[0] = "<img alt=\"No\" src=\"/system/templates/img/no.gif\" style=\"display: block; margin-left: auto; margin-right: auto;\">";
    $app[1] = "<img alt=\"Yes\" src=\"/system/templates/img/yes.gif\" style=\"display: block; margin-left: auto; margin-right: auto;\">";

    // organise criteria
    $who = $w->session('user_id');

    $module = Request::string("module");
    $reset = Request::string("reset");

    $where = '';
    if (empty($reset)) {
        if (!empty($module)) {
            $where .= " and r.module = " . $w->db->quote($module);
            $w->ctx("reqModule", $module);
        }
    }

    // get report categories from available report list
    $reports = ReportService::getInstance($w)->getReportsbyUserWhere($who, $where);

    // set headings based on role: 'user' sees only approved reports and no approval status
    $line = [["Title", "Module", "Description", ""]];

    // if i am a member of a list of reports, lets display them
    if ($reports) {
        foreach ($reports as $rep) {
            $member = ReportService::getInstance($w)->getReportMember($rep->id, $who);

            $edit_button = "";
            $duplicate_button = "";

            // editor & admin get EDIT button
            if ((!empty($member->role) && $member->role == "EDITOR") || (AuthService::getInstance($w)->user()->hasRole("report_admin"))) {
                $edit_button = HtmlBootstrap5::b($w->localUrl("/report/edit/" . $rep->id), "Edit", null, null, false, "btn btn-primary btn-sm");
                $duplicate_button = HtmlBootstrap5::b($w->localUrl("/report/duplicate/{$rep->id}"), "Duplicate", null, null, false, "btn btn-secondary btn-sm");
            }

            // admin also gets DELETE button
            if (AuthService::getInstance($w)->user()->hasRole("report_admin")) {
                $btndelete = HtmlBootstrap5::b($w->localUrl("/report/deletereport/" . $rep->id), "Delete", "Are you sure you want to delete this Report?", null, false, "btn btn-danger btn-sm");
            } else {
                $btndelete = "";
            }

            // if 'report user' only list approved reports with no approval status flag
            if ((AuthService::getInstance($w)->user()->hasRole("report_user")) && (!AuthService::getInstance($w)->user()->hasRole("report_editor")) && (!AuthService::getInstance($w)->user()->hasRole("report_admin"))) {
                if ($rep->is_approved == "1") {
                    $line[] = [
                        HtmlBootstrap5::a($w->localUrl("/report/runreport/" . $rep->id), StringSanitiser::sanitise($rep->title)),
                        ucfirst($rep->module),
                        StringSanitiser::stripTags($rep->description),
                        (!empty($edit_button) ? $edit_button : "")
                    ];
                }
            } else {
                // if editor or admin, list all active reports of which i have membership and show approval status and buttons
                $line[] = [
                    HtmlBootstrap5::a($w->localUrl("/report/runreport/" . $rep->id), StringSanitiser::sanitise($rep->title)),
                    ucfirst($rep->module),
                    StringSanitiser::stripTags($rep->description),
                    HtmlBootstrap5::buttonGroup($edit_button . $duplicate_button . $btndelete),
                ];
            }
        }
    } else {
        // i am not a member of any reports
        $line[] = ["You have no available reports", "", "", "", "", "", ""];
    }

    // populate search dropdowns
    $modules = ReportService::getInstance($w)->getModules();
    $w->ctx("modules", $modules);
    $type = [];
    $w->ctx("type", HtmlBootstrap5::select("type", $type));

    // display list of reports, if any
    $w->ctx("viewreports", HtmlBootstrap5::table($line, null, "tablesorter", true));
}
