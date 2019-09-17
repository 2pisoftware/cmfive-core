<?php

function edit_GET(Web &$w)
{
    $p = $w->pathMatch("id");
    $w->Report->navigation($w, (!empty($p['id']) ? "Edit" : "Create") . " Report");

    // Get or create report object
    $report = !empty($p['id']) ? $w->Report->getReport($p['id']) : new Report($w);
    if (!empty($p['id']) and empty($report->id)) {
        $w->error("Report not found", "/report");
    }

    if (empty($report)) {
        History::add("Create Report");
    } else {
        History::add("Edit Report: " . $report->title, null, $report);
    }

    $w->ctx("report", $report);

    $form = array(
        array((!empty($report->id) ? "Edit" : "Create a New") . " Report", "section"),
        array("Title", "text", "title", $report->title),
        array("Module", "select", "module", $report->module, $w->Report->getModules()),
        array("Description", "textarea", "description", $report->description, "110", "2"),
        array("Connection", "select", "report_connection_id", $report->report_connection_id, $w->Report->getConnections())
    );

    if (!empty($report)) {
        $sqlform = array(
            array("", "hidden", "title", $report->title),
            array("", "hidden", "module", $report->module),
            array("", "hidden", "description", $report->description),
            array("Code", "textarea", "report_code", $report->report_code, "110", "82", "codemirror"),
            array("", "hidden", "report_connection_id", $report->report_connection_id, $w->Report->getConnections())
        );
    }

    // DB view table
    $db_table = Html::form(array(
        array("Special Parameters", "section"),
        array("User", "static", "user", "{{current_user_id}}"),
        array("Roles", "static", "roles", "{{roles}}"),
        array("Site URL", "static", "webroot", "{{webroot}}"),
        array("View Database", "section"),
        array("Tables", "select", "dbtables", null, $w->Report->getAllDBTables()),
        array("Fields", "static", "dbfields", "<span id=\"dbfields\"></span>")
    ));

    $w->ctx("dbform", $db_table);

    if (!empty($report->id)) {
        $btnrun = Html::b("/report/runreport/" . $report->id, "Execute Report");
        $duplicate_button = Html::b($w->localUrl("/report/duplicate/{$report->id}"), "Duplicate");
        $w->ctx("btnrun", $btnrun);
        $w->ctx("duplicate_button", $duplicate_button);
    } else {
        $w->ctx("btnrun", "");
        $w->ctx("duplicate_button", "");
    }

    // Check access rights
    // If user is editing, we need to check multiple things, detailed in the helper function
    if (!empty($report->id)) {
        // Get the report member object for the logged in user
        $member = $w->Report->getReportMember($report->id, $w->Auth->user()->id);

        // Check if user can edit this report
        if (!$w->Report->canUserEditReport($report, $member)) {
            $w->error("You do not have access to this report", "/report");
        }
    } else {
        // If we're creating a report, check that the user has rights
        if ($w->Auth->user()->is_admin == 0 && !$w->Auth->user()->hasAnyRole(array('report_admin', 'report_editor'))) {
            $w->error("You do not have create report permissions", "/report");
        }
    }

    // Access checked and OK, add approval to form only if is report_admin or admin
    if ($w->Auth->user()->is_admin == 1 || $w->Auth->user()->hasRole("report_admin")) {
        $form[] = array("Approved", "checkbox", "is_approved", $report->is_approved);
    }

    $w->ctx("report_form", Html::form($form, $w->localUrl("/report/edit/{$report->id}"), "POST", "Save Report"));
    $w->ctx("sql_form", !empty($sqlform) ? Html::form($sqlform, $w->localUrl("/report/edit/{$report->id}"), "POST", "Save Report") : "");

    if (!empty($report->id)) {
        // ============= Members tab ===================
        $members = $w->Report->getReportMembers($report->id);

        // set columns headings for display of members
        $line[] = array("Member", "Is Email Recipient", "Role", "");

        // if there are members, display their full name, role and button to delete the member
        if ($members) {
            foreach ($members as $member) {
                $line[] = array(
                    $w->Report->getUserById($member->user_id),
                    $member->is_email_recipient ? "Yes" : "No",
                    $member->role,
                    Html::box("/report/editmember/" . $report->id . "/" . $member->user_id, " Edit ", true) .
                        Html::box("/report/deletemember/" . $report->id . "/" . $member->user_id, " Delete ", true)
                );
            }
        } else {
            // if there are no members, say as much
            $line[] = array("Group currently has no members. Please Add New Members.", "", "");
        }

        // display list of group members
        $w->ctx("viewmembers", Html::table($line, null, "tablesorter", true));

        // =========== template tab ======================

        $report_templates = $report->getTemplates();

        // Build table
        $table_header = array("Title", "Category", "Is Email Template", "Type", "Actions");
        $table_data = array();

        if (!empty($report_templates)) {
            // Add data to table layout
            foreach ($report_templates as $report_template) {
                $template = $report_template->getTemplate();
                $table_data[] = array(
                    $template->title,
                    $template->category,
                    $report_template->is_email_template ? "Yes" : "No",
                    $report_template->type,
                    Html::box("/report-templates/edit/{$report->id}/{$report_template->id}", "Edit", true) .
                        Html::b("/report-templates/delete/{$report_template->id}", "Delete", "Are you sure you want to delete this Report template entry?")
                );
            }
        }
        // Render table
        $w->ctx("templates_table", Html::table($table_data, null, "tablesorter", $table_header));
    }
}


function edit_POST(Web $w)
{
    $p = $w->pathMatch("id");

    $report = !empty($p['id']) ? $w->Report->getReport($p['id']) : new Report($w);
    if (!empty($p['id']) && empty($report->id)) {
        $w->error("Report not found", "/report");
    }

    // Check access rights
    // If user is editing, we need to check multiple things, detailed in the helper function
    if (!empty($report->id)) {
        // Get the report member object for the logged in user
        $member = $w->Report->getReportMember($report->id, $w->Auth->user()->id);

        // Check if user can edit this report
        if (!$w->Report->canUserEditReport($report, $member)) {
            $w->error("You do not have access to this report", "/report");
        }
    } else {
        // If we're creating a report, check that the user has rights
        if ($w->Auth->user()->is_admin == 0 and !$w->Auth->user()->hasAnyRole(array('report_admin', 'report_editor'))) {
            $w->error("You do not have create report permissions", "/report");
        }
    }

    // Insert or Update
    $report->fill($_POST);

    // Force select statements only
    $report->sqltype = "select";

    $report_connection_id = $w->request("report_connection_id");
    $report->report_connection_id = intval($report_connection_id);
    $response = $report->insertOrUpdate();

    // Handle the response
    if ($response === true) {
        // Add user to report members as owner if this is a new report
        if (empty($p['id'])) {
            $report_member = new ReportMember($w);
            $report_member->report_id = $report->id;
            $report_member->user_id = $w->Auth->user()->id;
            $report_member->role = "OWNER";
            $report_member->insert();
        }

        $w->msg("Report " . ($p['id'] ? "updated" : "created"), "/report/edit/{$report->id}");
    } else {
        $w->errorMessage($report, "Report", $response, $p['id'] ? true : false, "/report" . (!empty($account->id) ? "/edit/{$account->id}" : ""));
    }
}
