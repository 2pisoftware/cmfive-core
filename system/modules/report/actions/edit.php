<?php

function edit_GET(Web &$w) {
    $p = $w->pathMatch("id");
    $w->Report->navigation($w, (!empty($p['id']) ? __("Edit") : __("Create")) . __(" Report"));
    
    // Get or create report object
    $report = !empty($p['id']) ? $w->Report->getReport($p['id']) : new Report($w);
    if (!empty($p['id']) and empty($report->id)) {
        $w->error(__("Report not found"), "/report");
    }
    
    if (empty($report)) {
    	History::add(__("Create Report"));
    } else {
    	History::add(__("Edit Report: ").$report->title);
    }
    
    $w->ctx("report", $report);
    
    $form = array(
        array((!empty($report->id) ? __("Edit") : __("Create a New")) . __(" Report"), "section"),
        array(__("Title"), "text", "title", $report->title),
        array(__("Module"), "select", "module", $report->module, $w->Report->getModules()),
        array(__("Description"), "textarea", "description", $report->description, "110", "2"),
//        array("Code", "textarea", "report_code", $report->report_code, "110", "22", "codemirror"),
        array(__("Connection"), "select", "report_connection_id", $report->report_connection_id, $w->Report->getConnections())
    );
    
    if (!empty($report)) {
	    $sqlform = array(
	    		array("", "hidden", "title", $report->title),
	    		array("", "hidden", "module", $report->module),
	    		array("", "hidden", "description", $report->description),
	    		array(__("Code"), "textarea", "report_code", $report->report_code, "110", "82", "codemirror"),
	    		array("", "hidden", "report_connection_id", $report->report_connection_id, $w->Report->getConnections())
	    );
    }

    // DB view table
    $db_table = Html::form(array(
        array(__("Special Parameters"), "section"),
        array(__("User"), "static", "user", "{{current_user_id}}"),
        array(__("Roles"), "static", "roles", "{{roles}}"),
        array(__("Site URL"), "static", "webroot", "{{webroot}}"),
        array(__("View Database"), "section"),
        array(__("Tables"), "select", "dbtables", null, $w->Report->getAllDBTables()),
        array(__("Fields"), "static", "dbfields", "<span id=\"dbfields\"></span>")
    ));
    
    $w->ctx("dbform", $db_table);
    
    if (!empty($report->id)) {
    	$btnrun = Html::b("/report/runreport/" . $report->id, __("Execute Report"));
    	$w->ctx("btnrun", $btnrun);
    } else {
    	$w->ctx("btnrun", "");
    }
    
    // Check access rights
    // If user is editing, we need to check multiple things, detailed in the helper function
    if (!empty($report->id)) {
        // Get the report member object for the logged in user
        $member = $w->Report->getReportMember($report->id, $w->Auth->user()->id);
        
        // Check if user can edit this report
        if (!$w->Report->canUserEditReport($report, $member)) {
            $w->error(__("You do not have access to this report"), "/report");
        }
    } else {
        // If we're creating a report, check that the user has rights
        if ($w->Auth->user()->is_admin == 0 && !$w->Auth->user()->hasAnyRole(array('report_admin', 'report_editor'))) {
            $w->error(__("You do not have create report permissions"), "/report");
        }
    }
    
    // Access checked and OK, add approval to form only if is report_admin or admin
    if ($w->Auth->user()->is_admin == 1 || $w->Auth->user()->hasRole("report_admin")) {
        $form[0][] = array(__("Approved"), "checkbox", "is_approved", $report->is_approved);
    }
    
    $w->ctx("report_form", Html::form($form, $w->localUrl("/report/edit/{$report->id}"), "POST", __("Save Report")));
    $w->ctx("sql_form", !empty($sqlform) ? Html::form($sqlform, $w->localUrl("/report/edit/{$report->id}"), "POST", __("Save Report")) : "");
    
    if (!empty($report->id)) {
    	
    	// ============= Members tab ===================

        $members = $w->Report->getReportMembers($report->id);
        
        // set columns headings for display of members
        $line[] = array(__("Member"),__("Is Email Recipient"), __("Role"), "");

        // if there are members, display their full name, role and button to delete the member
        if ($members) {
            foreach ($members as $member) {
                $line[] = array(
                    $w->Report->getUserById($member->user_id),
					$member->is_email_recipient ? __("Yes") : __("No"),
                    $member->role,
                    Html::box("/report/editmember/".$report->id . "/". $member->user_id,__(" Edit "), true) .
                    Html::box("/report/deletemember/".$report->id."/".$member->user_id,__(" Delete "), true)
                );
            }
        } else {
            // if there are no members, say as much
            $line[] = array(__("Group currently has no members. Please Add New Members."), "", "");
        }

        // display list of group members
        $w->ctx("viewmembers",Html::table($line,null,"tablesorter",true));

        // =========== template tab ======================
        
        $report_templates = $report->getTemplates();
        
        // Build table
        $table_header = array(__("Title"), __("Category"), __("Is Email Template"), __("Type"), __("Actions"));
        $table_data = array();
        
        if (!empty($report_templates)) {
        
        	// Add data to table layout
        	foreach($report_templates as $report_template) {
        		$template = $report_template->getTemplate();
        		$table_data[] = array(
        				$template->title,
        				$template->category,
						$report_template->is_email_template ? "Yes" : "No",
        				$report_template->type,
        				Html::box("/report-templates/edit/{$report->id}/{$report_template->id}", __("Edit"), true) .
        				Html::b("/report-templates/delete/{$report_template->id}", __("Delete"), __("Are you sure you want to delete this Report template entry?"))
        		);
        	}
        }
        // Render table
        $w->ctx("templates_table", Html::table($table_data, null, "tablesorter", $table_header));        
    }
}


function edit_POST(Web $w) {
	$p = $w->pathMatch("id");
	
        $report = !empty($p['id']) ? $w->Report->getReport($p['id']) : new Report($w);
        if (!empty($p['id']) && empty($report->id)) {
            $w->error(__("Report not found"), "/report");
        }
        
        // Check access rights
        // If user is editing, we need to check multiple things, detailed in the helper function
        if (!empty($report->id)) {
            // Get the report member object for the logged in user
            $member = $w->Report->getReportMember($report->id, $w->Auth->user()->id);

            // Check if user can edit this report
            if (!$w->Report->canUserEditReport($report, $member)) {
                $w->error(__("You do not have access to this report"), "/report");
            }
        } else {
            // If we're creating a report, check that the user has rights
            if ($w->Auth->user()->is_admin == 0 and !$w->Auth->user()->hasAnyRole(array('report_admin', 'report_editor'))) {
                $w->error(__("You do not have create report permissions"), "/report");
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
            
            $w->msg(__("Report ") . ($p['id'] ? __("updated") : __("created")), "/report/edit/{$report->id}");
        } else {
            $w->errorMessage($report, __("Report"), $response, $p['id'] ? true : false, "/report" . (!empty($account->id) ? "/edit/{$account->id}" : ""));
        }
        
        
        
        
        // OLD CODE - REDUNDANT, KEEPING FOR FEED REFERENCE
/*        
        
	if (!array_key_exists("is_approved",$_REQUEST))
	$_REQUEST['is_approved'] = 0;

	// if there is a report ID in the URL ...
	if ($p['id']) {
		// get report details
		$rep = $w->Report->getReportInfo($p['id']);

		// if report exists, update it
		if ($rep) {
			$_POST['sqltype'] = $w->Report->getSQLStatementType($_POST['report_code']);
			$rep->fill($_POST);
                        $rep->report_connection_id = intval($_POST["report_connection_id"]);
			$rep->update();
			$repmsg = "Report updated.";

			// check if there is a feed associated with this report
			$feed = $w->Report->getFeedInfobyReportId($rep->id);
			if ($feed) {
				// if feed exists, need to reevaluate the URL in case of changes in the report parameters
				$elements = $rep->getReportCriteria();

				if ($elements) {
					foreach ($elements as $element) {
						if (($element[0] != "Description") && ($element[2] != ""))
						$query .= $element[2] . "=&lt;value&gt;&";
					}
				}

				$query = rtrim($query,"&");

				// use existing key to reevaluate feed URL
				$feedurl = $w->localUrl("/report/feed/?key=" . $feed->key . "&" . $query);

				// update feed URL
				$feed->url = $feedurl;
				$feed->update();
			}
		}
		else {
			$repmsg = "Report does not exist";
		}
	}
	else {
		$repmsg = "Report does not exist";
	}

	// return
	$w->msg($repmsg,"/report/viewreport/".$rep->id);
	
*/
}
