<?php

use Html\Cmfive\QuillEditor;

function edit_GET(Web &$w)
{
    $p = $w->pathMatch("id");
    ReportService::getInstance($w)->navigation($w, (!empty($p['id']) ? "Edit" : "Create") . " Report");

    // Get or create report object
    $report = !empty($p['id']) ? ReportService::getInstance($w)->getReport($p['id']) : new Report($w);
    if (!empty($p['id']) and empty($report->id)) {
        $w->error("Report not found", "/report");
    }

    // Check access rights
    // If user is editing, we need to check multiple things, detailed in the helper function
    if (!empty($report->id)) {
        // Get the report member object for the logged in user
        $member = ReportService::getInstance($w)->getReportMember($report->id, AuthService::getInstance($w)->user()->id);

        // Check if user can edit this report
        if (!ReportService::getInstance($w)->canUserEditReport($report, $member)) {
            $w->error("You do not have access to this report", "/report");
        }
    } else {
        // If we're creating a report, check that the user has rights
        if (AuthService::getInstance($w)->user()->is_admin == 0 && !AuthService::getInstance($w)->user()->hasAnyRole(['report_admin', 'report_editor'])) {
            $w->error("You do not have create report permissions", "/report");
        }
    }

    // Get list of categories from modules, JS in template is responsible for switching them around in UI
    $category_config = [];
    $category_config_for_select = [];
    foreach (Config::keys() ?? [] as $module) {
        $module_report_categories = Config::get($module . '.report.categories');
        if ($module_report_categories !== null) {
            $category_config[$module] = $module_report_categories;

            $categories_for_select = [];
            foreach ($module_report_categories as $key => $value) {
                $categories_for_select[] = [$value, $key];
            }
            $category_config_for_select[$module] = $categories_for_select;
        }
    }

    $w->ctx('category_config', $category_config);

    if (empty($report)) {
        History::add("Create Report");
    } else {
        History::add("Edit Report: " . $report->title, null, $report);
    }

    $w->ctx("report", $report);

    $form = [!empty($report->id) ? "Edit" : "Create a New" . " Report" =>
        [
            [["Title", "text", "title", StringSanitiser::sanitise($report->title)]],
            [
                (new \Html\Form\Select([
                    "id|name" => "module",
                    "label" => "Module",
                    "selected_option" => strtolower($report->module ?? ''),
                    "options" => ReportService::getInstance($w)->getModules(),
                ])),
                (new \Html\Form\Select([
                    'id|name' => 'category',
                    'label' => 'Category',
                    'selected_option' => StringSanitiser::sanitise($report->category),
                    'options' => !empty($category_config_for_select[$report->module]) ? $category_config_for_select[$report->module] : []
                ]))->setLabel('Category')
            ],
            [new QuillEditor([
                "id|name" => "description",
                "label" => "Description",
                "value" => $report->description,
            ])],
            [["Connection", "select", "report_connection_id", $report->report_connection_id, ReportService::getInstance($w)->getConnections()]],
        ]
    ];

    // Access checked and OK, add approval to form only if is report_admin or admin
    if (AuthService::getInstance($w)->user()->hasRole("report_admin")) {
        $section_form_title_key = array_keys($form);
        $form[$section_form_title_key[0] ?? 0][] = [["Approved", "checkbox", "is_approved", $report->is_approved]];
    }

    if (!empty($report)) {
        $sqlform = [
            "SQL Editor" => [
                // [new InputField\Hidden(["id|name" => "title", "value" => StringSanitiser::sanitise($report->title)])],
                // [new InputField\Hidden(["id|name" => "module", "value" => StringSanitiser::sanitise($report->module)])],
                // [new InputField\Hidden(["id|name" => "description", "value" => StringSanitiser::sanitise($report->description)])],
                // [new InputField\Hidden(["id|name" => "report_connection_id", "value" => $report->report_connection_id])],
                [new \Html\Cmfive\CodeMirrorEditor(["id|name" => "report_code", "value" => $report->report_code, "rows" => 10])],
            ]
        ];
    }

    // DB view table
    $db_table = HtmlBootstrap5::form([
        ["Special Parameters", "section"],
        ["User", "static", "user", "{{current_user_id}}"],
        ["Roles", "static", "roles", "{{roles}}"],
        ["Site URL", "static", "webroot", "{{webroot}}"],
        ["View Database", "section"],
        ["Tables", "select", "dbtables", null, ReportService::getInstance($w)->getAllDBTables()],
        ["Fields", "static", "dbfields", "<span id=\"dbfields\"></span>"],
    ]);

    $w->ctx("dbform", $db_table);

    if (!empty($report->id)) {
        $w->ctx("btnrun", HtmlBootstrap5::b(href: $w->localUrl("/report/runreport/" . $report->id), title: "Execute Report", class: "btn btn-primary"));
        $w->ctx("duplicate_button", HtmlBootstrap5::b(href: $w->localUrl("/report/duplicate/{$report->id}"), title: "Duplicate", class: "btn btn-secondary"));
    }

    $w->ctx("report_form", HtmlBootstrap5::multiColForm($form, $w->localUrl("/report/edit/{$report->id}"), "POST", "Save Report"));
    $w->ctx("sql_form", !empty($sqlform) ? HtmlBootstrap5::multiColForm($sqlform, $w->localUrl("/report/edit/{$report->id}"), "POST", "Save Report") : "");

    if (!empty($report->id)) {
        // ============= Members tab ===================
        $members = ReportService::getInstance($w)->getReportMembers($report->id);

        // set columns headings for display of members
        $line[] = ["Member", "Is Email Recipient", "Role", ""];

        // if there are members, display their full name, role and button to delete the member
        if ($members) {
            foreach ($members as $member) {
                $line[] = [
                    ReportService::getInstance($w)->getUserById($member->user_id),
                    $member->is_email_recipient ? "Yes" : "No",
                    $member->role,
                    HtmlBootstrap5::box(href: "/report/editmember/" . $report->id . "/" . $member->user_id, title: " Edit ", button: true, class: 'btn btn-secondary') .
                    HtmlBootstrap5::box(href: "/report/deletemember/" . $report->id . "/" . $member->user_id, title: " Delete ", button: true, class: 'btn btn-danger'),
                ];
            }
        } else {
            // if there are no members, say as much
            $line[] = ["Group currently has no members. Please Add New Members.", "", ""];
        }

        // display list of group members
        $w->ctx("viewmembers", HtmlBootstrap5::table($line, null, "tablesorter", true));

        // =========== template tab ======================
        $report_templates = $report->getTemplates();

        // Build table
        $table_header = ["Title", "Category", "Is Email Template", "Type", "Actions"];
        $table_data = [];

        if (!empty($report_templates)) {
            // Add data to table layout
            foreach ($report_templates as $report_template) {
                $template = $report_template->getTemplate();
                $table_data[] = [
                    StringSanitiser::sanitise($template->title),
                    StringSanitiser::sanitise($template->category),
                    $report_template->is_email_template ? "Yes" : "No",
                    $report_template->type,
                    HtmlBootstrap5::box("/report-templates/edit/{$report->id}/{$report_template->id}", "Edit", true) .
                    HtmlBootstrap5::b("/report-templates/delete/{$report_template->id}", "Delete", "Are you sure you want to delete this Report template entry?"),
                ];
            }
        }
        // Render table
        $w->ctx("templates_table", HtmlBootstrap5::table($table_data, null, "tablesorter", $table_header));
    }
}

function edit_POST(Web $w)
{
    $p = $w->pathMatch("id");

    $report = !empty($p['id']) ? ReportService::getInstance($w)->getReport($p['id']) : new Report($w);
    if (!empty($p['id']) && empty($report->id)) {
        $w->error("Report not found", "/report");
    }

    // Check access rights
    // If user is editing, we need to check multiple things, detailed in the helper function
    if (!empty($report->id)) {
        // Get the report member object for the logged in user
        $member = ReportService::getInstance($w)->getReportMember($report->id, AuthService::getInstance($w)->user()->id);

        // Check if user can edit this report
        if (!ReportService::getInstance($w)->canUserEditReport($report, $member)) {
            $w->error("You do not have access to this report", "/report");
        }
    } else {
        // If we're creating a report, check that the user has rights
        if (AuthService::getInstance($w)->user()->is_admin == 0 and !AuthService::getInstance($w)->user()->hasAnyRole(['report_admin', 'report_editor'])) {
            $w->error("You do not have create report permissions", "/report");
        }
    }

    // Insert or Update
    $report->fill($_POST);

    // Force select statements only
    $report->sqltype = "select";

    $report->report_connection_id = intval(Request::int("report_connection_id"));
    $response = $report->insertOrUpdate(true);

    // Handle the response
    if ($response === true) {
        // Add user to report members as owner if this is a new report
        if (empty($p['id'])) {
            $report_member = new ReportMember($w);
            $report_member->report_id = $report->id;
            $report_member->user_id = AuthService::getInstance($w)->user()->id;
            $report_member->role = "OWNER";
            $report_member->insert();
        }

        $w->msg("Report " . ($p['id'] ? "updated" : "created"), "/report/edit/{$report->id}");
    } else {
        $w->errorMessage($report, "Report", $response, $p['id'] ? true : false, "/report" . (!empty($report->id) ? "/edit/{$report->id}" : ""));
    }
}
