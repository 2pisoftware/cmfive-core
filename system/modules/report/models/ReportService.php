<?php

class ReportService extends DbService {
    static private $tables;
    
    public function getReport($id) {
        return $this->getObject("Report", $id);
    }
    
    public function getReports() {
        return $this->getObjects("Report", array("is_deleted" => 0));
    }
    
    
    
    // return list of members attached to a report for given report ID
    function getReportMembers($id) {
        return $this->getObjects("ReportMember", array("report_id" => $id, "is_deleted" => 0));
    }

    // return member for given report ID and user id
    function getReportMember($id, $uid) {
        return $this->getObject("ReportMember", array("report_id" => $id, "user_id" => $uid, "is_deleted" => 0));
    }
    
    // Helper function to decide whether or not a user has access to a given report
    function canUserEditReport($report, $member) {
        // First, is logged in user a system admin
        if ($this->w->Auth->user()->is_admin == 1) {
            return true;
        }
        
        // Check if logged in user is report_admin
        if ($this->w->Auth->user()->hasRole("report_admin")) {
            return true;
        }
        
		if (empty($report->id) || empty($member->id)) {
            return false;
        }
        
        // Then check if the user has report_editor role
        if (!$this->w->Auth->user()->hasRole("report_editor")) {
            return false;
        }
        
        // Check that the member given is for the given report
        if ($report->id !== $member->report_id) {
            // Log this event
            $this->w->Log->error("Wrong member given for report (In ReportService, line: " . __LINE__ . ")");
            return false;
        }
        
        // User is report_editor, check if this report is theirs or that they have edit access
        if ($member->role === "OWNER" or $member->role === "EDITOR") {
            return true;
        }
        
        return false;
    }
    
    
    /**
     * Returns array of connection objects
     * 
     * @return Array connections
     */
    public function getConnections() {
        return $this->getObjects("ReportConnection", array("is_deleted" => "0"));
    }

    public function getConnection($id) {
        return $this->getObject("ReportConnection", array("id" => $id, "is_deleted" => "0"));
    }
    
    // function to sort lists by date schedule
    static function sortBySchedule($a, $b) {
        if ($a->dt_schedule == $b->dt_schedule) {
            return 0;
        }
        return ($a->dt_schedule < $b->dt_schedule) ? +1 : -1;
    }

    // get list of modules for Html::select
    function getModules() {
        $modules = $this->w->modules();
        if ($modules) {
            foreach ($modules as $f) {
                $modules2[] = array(ucfirst($f), $f);
            }
            sort($modules2);
            return $modules2;
        }
    }

    // static list of group permissions
    function getReportPermissions() {
        return array("USER", "EDITOR");
    }
    
    // return a report given its ID
    function getReportInfo($id) {
        return $this->getObject("Report", array("id" => $id));
    }

    // return list of feeds
    function getFeeds() {
        return $this->getObjects("ReportFeed", array("is_deleted" => 0));
    }

    // return a feed given its id
    function getFeedInfobyId($id) {
        return $this->getObject("ReportFeed", array("id" => $id, "is_deleted" => 0));
    }

    // return a feed given its report id
    function getFeedInfobyReportId($id) {
        return $this->getObject("ReportFeed", array("report_id" => $id, "is_deleted" => 0));
    }

    // return a feed given its key
    function getFeedInfobyKey($key) {
        return $this->getObject("ReportFeed", array("key" => $key, "is_deleted" => 0));
    }

    // return list of APPROVED and NOT DELETED report IDs for a given a user ID and a where clause
    function getReportsbyUserWhere($id, $where) {
		
		// Clause for admin user
		if ($this->w->Auth->user()->is_admin == '1' || $this->w->Auth->user()->hasRole("report_admin")) {
			return $this->getReports();
		}
		
        // need to get reports for me and my groups
        // me
        $myid[] = $id;

        // need to check all groups given group member could be a group
        $groups = $this->w->Auth->getGroups();

        if ($groups) {
            foreach ($groups as $group) {
                $flg = $this->w->Auth->user()->inGroup($group);
                if ($flg)
                    $myid[$group->id] = $group->id;
            }
        }
        // list of IDs to check for report membership, my ID and my group IDs
        $theid = implode(",", $myid);
        
        // the sql statement below return duplicate reports if they have multiple members
        
        /*
        $results = $this->_db->get("report")->select("report.*")
                    ->leftJoin("report_member on report_member.report_id = report.id")
                    ->where("report_member.user_id", $myid)->where($where)
                    ->where("report.is_deleted", 0)->where("report_member.is_deleted", 0)
                    ->order_by("report.is_approved desc, report.title")->fetch_all();
        */
        
        // this sql below statement may not be as nifty as the above .. but it works!
        
        $rows = $this->_db->sql("SELECT distinct r.* from " . ReportMember::$_db_table . " as m inner join " . 
                Report::$_db_table . " as r on m.report_id = r.id where m.user_id in (" . $theid . ") " . $where . 
                " and r.is_deleted = 0 order by r.is_approved desc,r.title")->fetch_all();
        return $this->fillObjects("Report", $rows);
    }

    // return list of APPROVED and NOT DELETED report IDs for a given a user ID as member
    function getReportsbyUserId($id) {
        // need to get reports for me and my groups
        // me
        $myid[] = $id;

        // need to check all groups given group member could be a group
        $groups = $this->w->Auth->getGroups();

        if ($groups) {
            foreach ($groups as $group) {
                if ($this->w->Auth->user()->inGroup($group)) {
                    $myid[$group->id] = $group->id;
				}
            }
        }
        // list of IDs to check for report membership, my ID and my group IDs
//        $id = implode(",", $myid);
        $results = $this->_db->get("report_member")->select("report.*")
                    ->leftJoin("report on report_member.report_id = report.id")
                    ->where("report_member.user_id", $myid)
                    ->where("report.is_deleted", 0)->where("report_member.is_deleted", 0)
                    ->order_by("report.is_approved desc, report.title")->fetch_all();
//        $rows = $this->_db->sql("SELECT distinct m.report_id from " . ReportMember::$_db_table . " as m inner join " . Report::$_db_table . " as r on m.report_id = r.id where m.user_id in (" . $id . ") and r.is_deleted = 0 and m.is_deleted = 0 order by r.is_approved desc,r.title")->fetch_all();
        return $this->fillObjects("ReportMember", $results);
    }

    // return list of APPROVED and NOT DELETED report IDs for a given a user ID and Module
    function getReportsbyModuleId() {
        // need to get reports for me and my groups
        // me
        $myid[] = $this->w->session('user_id');

        // need to check all groups given group member could be a group
        $groups = $this->w->Auth->getGroups();

        if ($groups) {
            foreach ($groups as $group) {
                $flg = $this->w->Auth->user()->inGroup($group);
                if ($flg)
                    $myid[$group->id] = $group->id;
            }
        }
        // list of IDs to check for report membership, my ID and my group IDs
        $id = implode(",", $myid);
        $module = $this->w->currentModule();

        $results = $this->_db->get("report_member")->select("report.*")
                    ->leftJoin("report on report_member.report_id = report.id")
                    ->where("report_member.user_id", $myid)->where("report.module", $module)
                    ->where("report.is_deleted", 0)->where("report_member.is_deleted", 0)
                    ->order_by("report.is_approved desc, report.title")->fetch_all();
        // $rows = $this->_db->sql("SELECT distinct r.id,r.title from " . ReportMember::$_db_table . " as m inner join " . Report::$_db_table . " as r on m.report_id = r.id where m.user_id in (" . $id . ") and r.module = '" . $module . "' and r.is_deleted = 0 and m.is_deleted = 0 order by r.is_approved desc,r.title")->fetch_all();
        return $this->fillObjects("Report", $results);
    }

    // return menu links of APPROVED and NOT DELETED report IDs for a given a user ID as member
    function getReportsforNav() {
        $repts = array();
        $reports = $this->getReportsbyModuleId();

        if ($reports) {
            foreach ($reports as $report) {
                $this->w->menuLink("report/runreport/" . $report->id, $report->title, $repts);
            }
        }
        return $repts;
    }


    // return a users full name given their user ID
    function getUserById($id) {
        $u = $this->w->Auth->getUser($id);
        return $u ? $u->getFullName() : "";
    }

    // for parameter dropdowns, run SQL statement and return an array(value,title) for display
    // DANGEROUS
    function getFormDatafromSQL($sql, $connection = null) {
        if (!empty($connection)) {
            $rows = $connection->query($sql)->fetchAll();
        } else {
            $rows = $this->_db->sql($sql)->fetch_all();
        }
        if ($rows) {
            foreach ($rows as $row) {
                $arr[] = array($row['title'], $row['value']);
            }
            return $arr;
        }
        return null;
    }

    // given a report SQL statement, return recordset
    // DANGEROUS
    function getExefromSQL($sql, $connection = null) {
        if (!empty($connection)) {
            return $connection->query($sql)->execute();
        } else {
            return $this->_db->sql($sql)->execute();
        }
    }

    // convert dd/mm/yyyy date to yyy-mm-dd for SQL statements
    function date2db($date) {
        if ($date) {
            list($d, $m, $y) = preg_split("/\/|-|\./", $date);
            return $y . "-" . $m . "-" . $d;
        }
    }

    // return all tables in the DB for display
    function getAllDBTables() {
        $dbtbl = array();
        foreach($this->_db->_query("show tables")->fetchAll(PDO::FETCH_NUM) as $table) {
            $dbtbl[] = $table[0];
        }
        ReportService::$tables = $dbtbl;
//        $sql = "show tables in " . $this->_db->getDatabase();
//        $tbls = $this->_db->sql($sql)->fetch_all();

//        if ($tbls) {
//            foreach ($tbls as $tbl) {
//                $dbtbl[] = array($tbl['Tables_in_' . $this->_db->getDatabase()], $tbl['Tables_in_' . $this->_db->getDatabase()]);
//            }
//        }
        return $dbtbl;
    }

    // return array of fields/type in a given table
    function getFieldsinTable($table) {
        $output = "";
        
        if (empty(ReportService::$tables)) {
            $this->getAllDBTables();
        }
        
        // Check that the table actually exists, reduces chance for SQL injection
        if (!in_array(strtolower($table), ReportService::$tables)) {
            return "";
        }
        
        if ($table != "") {
            
            $sql = "show columns in " . $table;
            $fields = $this->_db->sql($sql)->fetch_all();

            if ($fields) {
                $output = "<table>";
                $output .= "<tr><td><b>Field</b></td><td><b>Type</b></td></tr>";
                foreach ($fields as $field) {
                    $output .= "<tr><td>" . $field['Field'] . "</td><td>" . $field['Type'] . "</td></tr>";
                }
                $output .= "</table>";
            }
        }
        
        return $output;
    }

    function getSQLStatementType($report_code) {
        // return our list of SQL statements
        //		preg_match_all("/@@[a-zA-Z0-9_\s\|,;\(\)\{\}<>\/\-='\.@:%\+\*\$]*?@@/",preg_replace("/\n/"," ",$report_code), $arrsql);
        preg_match_all("/@@.*?@@/", preg_replace("/\n/", " ", $report_code), $arrsql);

        // if we have statements, continue ...
        $action = "";
        if ($arrsql) {
            foreach ($arrsql as $sql) {
                if ($sql) {
                    foreach ($sql as $s) {
                        list($title, $sql) = preg_split("/\|\|/", $s);
                        // put on one line just to be sure
                        $sql = preg_replace("/\n/", " ", trim($sql));
                        $arr = preg_split("/\s/", $sql);
                        $action .= $arr[0] . ", ";
                    }
                }
            }
            $action = rtrim($action, ", ");

            // return comma delimited string of actions of SQL for display only
            return $action;
        } else {
            return "No action Found";
        }
    }

    // create an array of available report output formats for inclusion in the parameters form
    function selectReportFormat() {
        $arr = array();
        $arr[] = array("Web Page", "html");
        $arr[] = array("Comma Delimited File", "csv");
        $arr[] = array("PDF File", "pdf");
        $arr[] = array("XML", "xml");
        return array(array("Format", "select", "format", null, $arr));
    }

    // export a recordset as CSV
    function exportcsv($rows, $title) {
        // require the necessary library
        require_once("parsecsv/parsecsv.lib.php");

        // set filename
        $filename = str_replace(" ", "_", $title) . "_" . date("Y.m.d-H.i") . ".csv";

        // if we have records, comma delimit the fields/columns and carriage return delimit the rows
        if (!empty($rows)) {
            foreach ($rows as $row) {
                //throw away the first line which list the form parameters
                $crumbs = array_shift($row);
                $title = array_shift($row);
                $hds = array_shift($row);
                $hvals = array_values($hds);

                // find key of any links
                foreach ($hvals as $h) {
                    if (stripos($h, "_link")) {
                        list($fld, $lnk) = preg_split("/_/", $h);
                        $ukey[] = array_search($h, $hvals);
                        unset($hds[$h]);
                    }
                }

                // iterate row to build URL. if required
                if (!empty($ukey)) {
                    foreach ($row as $r) {
                        foreach ($ukey as $n => $u) {
                            // dump the URL related fields for display
                            unset($r[$u]);
                        }
                        $arr[] = $r;
                    }
                    $row = $arr;
                    unset($arr);
                }

                $csv = new parseCSV();
                $this->w->out($csv->output($filename, $row, $hds));
                unset($ukey);
            }
            $this->w->sendHeader("Content-type", "application/csv");
            $this->w->sendHeader("Content-Disposition", "attachment; filename=" . $filename);
            $this->w->setLayout(null);
        }
    }

    // export a recordset as PDF
    function exportpdf($rows, $title, $report_template = null) {
        $filename = str_replace(" ", "_", $title) . "_" . date("Y.m.d-H.i") . ".pdf";

        // using TCPDF so grab includes
        require_once('tcpdf/tcpdf.php');

        // instantiate and set parameters
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle($title);
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        //$pdf->setLanguageArray($l);
        // no header, set font and create a page
        $pdf->setPrintHeader(false);
        $pdf->SetFont("helvetica", "B", 9);
        $pdf->AddPage();

        // title of report
        $hd = "<h1>" . $title . "</h1>";
        $pdf->writeHTMLCell(0, 10, 60, 15, $hd, 0, 1, 0, true);
        $created = date("d/m/Y g:i a");
        $pdf->writeHTMLCell(0, 10, 60, 25, $created, 0, 1, 0, true);
		
        // display recordset
		
		
        if (!empty($rows)) {
			if (empty($report_template)) {
				foreach ($rows as $row) {
					//throw away the first line which list the form parameters
					$crumbs = array_shift($row);
					$title = array_shift($row);
					$hds = array_shift($row);
					$hds = array_values($hds);

					$results = "<h3>" . $title . "</h3>";
					$results .= "<table cellpadding=2 cellspacing=2 border=0 width=100%>\n";
					foreach ($row as $r) {
						$i = 0;
						foreach ($r as $field) {
							if (!stripos($hds[$i], "_link")) {
								$results .= "<tr><td width=20%>" . $hds[$i] . "</td><td>" . $field . "</td></tr>\n";
							}
							$i++;
						}
						$results .= "<tr><td colspan=2><hr /></td></tr>\n";
					}
					$results .= "</table><p>";
					$pdf->writeHTML($results, true, false, true, false);
				}
			} else {
				$templatedata = array();
				foreach($rows as $row) {
					$crumbs = array_shift($row);
					$title = array_shift($row);
					$hds = array_shift($row);
					$hds = array_values($hds);

					$templatedata[] = array("title" => $title, "headers" => $hds, "results" => $row);
				}

				if (!empty($report_template) && !empty($templatedata)) {
					$results = $this->w->Template->render(
							$report_template->template_id,
							array("data" => $templatedata, "w" => $this->w, "POST" => $_POST));     

					$pdf->writeHTML($results, true, false, true, false);
				}
			}
		}

        // set for 'open/save as...' dialog
        $pdf->Output($filename, 'D');
    }

    // export a recordset as XML
    function exportxml($rows, $title) {
        $filename = str_replace(" ", "_", $title) . "_" . date("Y.m.d-H.i") . ".xml";

        $this->w->out("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
        $this->w->out("<report>\n");
        $this->w->out("\t<title>" . $title . "</title>\n");
        $this->w->out("\t<created>" . date("d/m/Y h:i:s") . "</created>\n");

        // if we have records ...
        if (!empty($rows)) {
            foreach ($rows as $row) {
                //throw away the first line which list the form parameters
                $crumbs = array_shift($row);
                $title = array_shift($row);
                $hds = array_shift($row);
                $hds = array_values($hds);

                $this->w->out("\t<rows title=\"" . $title . "\">\n");

                foreach ($row as $r) {
                    $this->w->out("\t\t<row>\n");
                    $i = 0;
                    foreach ($r as $field) {
                        if (!stripos($hds[$i], "_link")) {
                            $this->w->out("\t\t\t<" . preg_replace("/\s+/", "", $hds[$i]) . ">" . htmlentities($field) . "</" . preg_replace("/\s+/", "", $hds[$i]) . ">\n");
                        }
                        $i++;
                    }
                    $this->w->out("\t\t</row>\n");
                }
                $this->w->out("\t</rows>\n");
            }
        }
        $this->w->out("</report>\n");

        // set header for 'open/save as...' dialog
        $this->w->sendHeader("Content-type", "application/xml");
        $this->w->sendHeader("Content-Disposition", "attachment; filename=" . $filename);
        $this->w->setLayout(null);
    }

    // function to substitute special terms
    function putSpecialSQL($sql) {
        if ($sql != "") {
            $special = array();
            $replace = array();

            // get user roles
            $usr = $this->w->Auth->user();
            $roles = '';
            foreach ($usr->getRoles() as $role) {
                $roles .= "'" . $role . "',";
            }
            $roles = rtrim($roles, ",");

            // $special must be in terms of a regexp for preg_match
            $special[0] = "/\{\{current_user_id\}\}/";
            $replace[0] = $_SESSION["user_id"];
            $special[1] = "/\{\{roles\}\}/";
            $replace[1] = $roles;
            $special[2] = "/\{\{webroot\}\}/";
            $replace[2] = $this->w->localUrl();

            // replace and return
            return preg_replace($special, $replace, $sql);
        }
    }

    // function to check syntax of report SQL statememnt
    function getcheckSQL($sql, PDO $connection = null) {
        // checking for rows will return false if no data is returned, even if SQL is ok
        // so let's just run the statement and try to catch any exceptions otherwise SQL runs ok
        try {
            if (empty($connection)) {
                $this->startTransaction();
                $rows = $this->getExefromSQL($sql);
                $this->rollbackTransaction();
                return true;
            } else {
                $connection->beginTransaction();
                $rows = $connection->query($sql)->execute();
                $connection->rollBack();
                return true;
            }
        } catch (Exception $e) {
            $this->Log->error($e->getMessage());
            // SQL returns errors so clean up and return false
            if (empty($connection)) {
                $this->rollbackTransaction();
                return false;
            } else {
                $connection->rollBack();
                return false;
            }
        }
    }

    public function getReportTemplate($id) {
        return $this->getObject("ReportTemplate", $id);
    }
    
    // build the Report navigation
    public function navigation(Web $w, $title = null, $nav = null) {
        if (!empty($title)) {
            $w->ctx("title", $title);
        }

        $nav = $nav ? $nav : array();

        if ($w->Auth->loggedIn()) {
            $w->menuLink("report/index", "Report Dashboard", $nav);

            if ($w->Auth->user()->hasRole("report_editor") || $w->Auth->user()->hasRole("report_admin")) {
                $w->menuLink("report/edit", "Create a Report", $nav);
                $w->menuLink("report-connections", "Connections", $nav);
                $w->menuLink("report/listfeed", "Feeds Dashboard", $nav);
            }
        }

        $w->ctx("navigation", $nav);
        return $nav;
    }

}
