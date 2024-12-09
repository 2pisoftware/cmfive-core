<?php

class ReportService extends DbService
{
    private static $tables;

    public function getReport($id)
    {
        return $this->getObject("Report", $id);
    }

    public function getReports()
    {
        return $this->getObjects("Report", array("is_deleted" => 0));
    }

    public function getReportByModuleAndCategory($module, $category)
    {
        return $this->getObject('Report', ['module' => $module, 'category' => $category, 'is_deleted' => 0]);
    }

    // return list of members attached to a report for given report ID
    public function getReportMembers($id)
    {
        return $this->getObjects("ReportMember", array("report_id" => $id, "is_deleted" => 0));
    }

    // return member for given report ID and user id
    public function getReportMember($id, $uid)
    {
        $conferred = [];
        $conferred[] = $this->getObject("ReportMember", ["report_id" => $id, "user_id" => $uid, "is_deleted" => 0]);
        $groups = AuthService::getInstance($this->w)->getGroups();

        foreach ($groups ?? [] as $group) {
            if (AuthService::getInstance($this->w)->getUser($uid)->inGroup($group)) {
                $conferred[] = $this->getObject("ReportMember", ["report_id" => $id, "user_id" => $group->id, "is_deleted" => 0]);
            }
        }
        $conferred = array_filter($conferred);
        return end($conferred);
    }

    // Helper function to decide whether or not a user has access to a given report
    public function canUserEditReport($report, $member)
    {
        // First, is logged in user a system admin
        if (AuthService::getInstance($this->w)->user()->is_admin == 1) {
            return true;
        }

        // Check if logged in user is report_admin
        if (AuthService::getInstance($this->w)->user()->hasRole("report_admin")) {
            return true;
        }

        if (empty($report->id) || empty($member->id)) {
            return false;
        }

        // Then check if the user has report_editor role
        if (!AuthService::getInstance($this->w)->user()->hasRole("report_editor")) {
            return false;
        }

        // Check that the member given is for the given report
        if ($report->id !== $member->report_id) {
            // Log this event
            LogService::getInstance($this->w)->error("Wrong member given for report (In ReportService, line: " . __LINE__ . ")");
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
    public function getConnections()
    {
        return $this->getObjects("ReportConnection", array("is_deleted" => "0"));
    }

    public function getConnection($id)
    {
        return $this->getObject("ReportConnection", array("id" => $id, "is_deleted" => "0"));
    }

    // function to sort lists by date schedule
    public static function sortBySchedule($a, $b)
    {
        if ($a->dt_schedule == $b->dt_schedule) {
            return 0;
        }
        return ($a->dt_schedule < $b->dt_schedule) ? +1 : -1;
    }

    // get list of modules for Html::select
    public function getModules()
    {
        $modules = $this->w->modules();
        $parsed_modules = [];
        foreach ($modules ?? [] as $module) {
            $parsed_modules[] = [ucfirst($module), $module];
        }
        sort($parsed_modules);
        return $parsed_modules;
    }

    // static list of group permissions
    public function getReportPermissions()
    {
        return array("USER", "EDITOR");
    }

    // return a report given its ID
    public function getReportInfo($id)
    {
        return $this->getObject("Report", array("id" => $id));
    }

    // return list of feeds
    public function getFeeds()
    {
        return $this->getObjects("ReportFeed", array("is_deleted" => 0));
    }

    // return a feed given its id
    public function getFeedInfobyId($id)
    {
        return $this->getObject("ReportFeed", array("id" => $id, "is_deleted" => 0));
    }

    // return a feed given its report id
    public function getFeedInfobyReportId($id)
    {
        return $this->getObject("ReportFeed", array("report_id" => $id, "is_deleted" => 0));
    }

    // return a feed given its key
    public function getFeedInfobyKey($key)
    {
        return $this->getObject("ReportFeed", array("report_key" => $key, "is_deleted" => 0));
    }

    // return list of APPROVED and NOT DELETED report IDs for a given a user ID and a where clause
    public function getReportsbyUserWhere($id, $where)
    {
        // Clause for admin user
        if (AuthService::getInstance($this->w)->user()->hasRole("report_admin")) {
            return $this->getReports();
        }

        // need to get reports for me and my groups
        // me
        $myid = [$this->_db->quote($id)];

        // need to check all groups given group member could be a group
        $groups = AuthService::getInstance($this->w)->getGroups();

        foreach ($groups ?? [] as $group) {
            if (AuthService::getInstance($this->w)->user()->inGroup($group)) {
                $myid[$group->id] = $this->_db->quote($group->id);
            }
        }

        // list of IDs to check for report membership, my ID and my group IDs
        $theid = implode(",", $myid);

        $filter = $this->unitaryWhereToAndClause($where);

        $rows = $this->_db->sql("SELECT distinct r.* from " . ReportMember::$_db_table . " as m inner join " .
            Report::$_db_table . " as r on m.report_id = r.id " .
            " where m.user_id in (" . $theid . ") " . $filter .
            " and r.is_deleted = 0 and m.is_deleted = 0 " .
            " order by r.is_approved desc,r.title")->fetchAll();
        return $this->fillObjects("Report", $rows);
    }

    /**
     * unitary approach to form an 'and' clause for 'where' from text or key values
     *
     * @param string $where
     * @param array $where
     * @return string
     */
    public function unitaryWhereToAndClause($where)
    {
        // adapt if we were given raw SQL!
        if (!is_array($where)) {
            // assume we only check a single equality/pair
            $spec = explode("=", $where);
            // anything else will be turned to mush
            $column = explode(" ", trim($spec[0]));
            $column = explode(".", end($column));
            $match = trim(end($spec));
            $match = str_replace("'", "", $match);
            $where = [
                end($column) => $match
            ];
        }
        $filter="";
        // enforce literal quoted match as r.[columnName] = 'something'
        foreach ($where as $term => $check) {
            if (!empty($check)) {
                $tmp=explode(".", $term);
                $term=trim(end($tmp));
                $tmp=explode(" ", $term);
                $term=trim(end($tmp));
                $check = str_replace("'", "", $check);
                $term = str_replace("'", "", $term);
                $term = str_replace("--", "", $term);
                $term = str_replace(";", "", $term);
                $filter .= " and r.".$term." = ".$this->_db->quote($check)." ";
            }
        }
        return $filter;
    }
    // return list of APPROVED and NOT DELETED report IDs for a given a user ID as member
    public function getReportsbyUserId($id)
    {
        // need to get reports for me and my groups
        // me
        $myid[] = $id;

        // need to check all groups given group member could be a group
        $groups = AuthService::getInstance($this->w)->getGroups();

        if ($groups) {
            foreach ($groups as $group) {
                if (AuthService::getInstance($this->w)->user()->inGroup($group)) {
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
            ->orderBy("report.is_approved desc, report.title")->fetchAll();
        return $this->fillObjects("ReportMember", $results);
    }

    // return list of APPROVED and NOT DELETED report IDs for a given a user ID and Module
    public function getReportsbyModuleId()
    {
        // need to get reports for me and my groups
        // me
        $myid[] = $this->w->session('user_id');

        // need to check all groups given group member could be a group
        $groups = AuthService::getInstance($this->w)->getGroups();

        if ($groups) {
            foreach ($groups as $group) {
                $flg = AuthService::getInstance($this->w)->user()->inGroup($group);
                if ($flg) {
                    $myid[$group->id] = $group->id;
                }
            }
        }
        // list of IDs to check for report membership, my ID and my group IDs
        $id = implode(",", $myid);
        $module = $this->w->currentModule();

        $results = $this->_db->get("report_member")->select("report.*")
            ->leftJoin("report on report_member.report_id = report.id")
            ->where("report_member.user_id", $myid)->where("report.module", $module)
            ->where("report.is_deleted", 0)->where("report_member.is_deleted", 0)
            ->orderBy("report.is_approved desc, report.title")->fetchAll();

        return $this->fillObjects("Report", $results);
    }

    // return menu links of APPROVED and NOT DELETED report IDs for a given a user ID as member
    public function getReportsforNav()
    {
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
    public function getUserById($id)
    {
        $u = AuthService::getInstance($this->w)->getUser($id);
        return $u ? $u->getFullName() : "";
    }

    // for parameter dropdowns, run SQL statement and return an array(value,title) for display
    // DANGEROUS
    public function getFormDatafromSQL($sql, $connection)
    {
        $rows = $connection->query(trim($sql))->fetchAll();

        $arr = [];
        if ($rows) {
            foreach ($rows as $row) {
                $arr[] = [$row['title'], $row['value']];
            }
        }
        return $arr;
    }

    // given a report SQL statement, return recordset
    // DANGEROUS
    public function getExefromSQL($sql, $connection = null)
    {
        return $connection->query($sql)->execute();
    }

    // convert dd/mm/yyyy date to yyy-mm-dd for SQL statements
    public function date2db($date)
    {
        if ($date) {
            list($d, $m, $y) = preg_split("/\/|-|\./", $date);
            return $y . "-" . $m . "-" . $d;
        }
    }

    // return all tables in the DB for display
    public function getAllDBTables()
    {
        $dbtbl = array();
        foreach ($this->_db->_query("show tables")->fetchAll(PDO::FETCH_NUM) as $table) {
            $dbtbl[] = $table[0];
        }
        ReportService::$tables = $dbtbl;

        return $dbtbl;
    }

    // return array of fields/type in a given table
    public function getFieldsinTable($table)
    {
        $output = "";

        if (empty(ReportService::$tables)) {
            $this->getAllDBTables();
        }

        // Check that the table actually exists, reduces chance for SQL injection
        if (!in_array(strtolower($table), ReportService::$tables)) {
            return "";
        }

        if ($table != "") {
            $fields = $this->_db->sql("show columns in " . $table)->fetchAll();

            if ($fields) {
                $output = "<table><tr><td><b>Field</b></td><td><b>Type</b></td></tr>";
                foreach ($fields as $field) {
                    $output .= "<tr><td>" . $field['Field'] . "</td><td>" . $field['Type'] . "</td></tr>";
                }
                $output .= "</table>";
            }
        }

        return $output;
    }

    public function getSQLStatementType($report_code)
    {
        // return our list of SQL statements
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
            return rtrim($action, ", ");
        } else {
            return "No action Found";
        }
    }

    // create an array of available report output formats for inclusion in the parameters form
    public function selectReportFormat()
    {
        $arr = [
            ["Web Page", "html"],
            ["Comma Delimited File", "csv"],
            ["PDF File", "pdf"],
            ["XML", "xml"],
        ];

        return [["Format", "select", "format", null, $arr]];
    }

    // export a recordset as CSV
    public function exportcsv($rows, $title)
    {
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

                $csv = new ParseCsv\Csv();
                $csv->output_filename = $filename;
                // ignore lib wrapper csv->output, to keep control over header re-sends!
                $this->w->out($csv->unparse($row, $hds, null, null, null));
                // can't use this way without commenting out header section, which composer won't like
                // $this->w->out($csv->output($filename, $row, $hds));
                unset($ukey);
            }
            $this->w->sendHeader("Content-type", "application/csv");
            $this->w->sendHeader("Content-Disposition", "attachment; filename=" . $filename);
            $this->w->setLayout(null);
        }
    }

    // export a recordset as PDF
    public function exportpdf($rows, $title, $report_template = null)
    {
        $filename = str_replace(" ", "_", $title) . "_" . date("Y.m.d-H.i") . ".pdf";

        // using TCPDF, but sourcing from Composer
        //require_once('tcpdf/tcpdf.php');

        // instantiate and set parameters
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle($title);
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
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
                foreach ($rows as $row) {
                    $crumbs = array_shift($row);
                    $title = array_shift($row);
                    $hds = array_shift($row);
                    $hds = array_values($hds);

                    $templatedata[] = array("title" => $title, "headers" => $hds, "results" => $row);
                }

                if (!empty($report_template) && !empty($templatedata)) {
                    $results = TemplateService::getInstance($this->w)->render(
                        $report_template->template_id,
                        array("data" => $templatedata, "w" => $this->w, "POST" => $_POST)
                    );

                    $pdf->writeHTML($results, true, false, true, false);
                }
            }
        }

        // set for 'open/save as...' dialog
        $pdf->Output($filename, 'D');
    }

    // export a recordset as XML
    public function exportxml($rows, $title)
    {
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
    public function putSpecialSQL($sql)
    {
        if ($sql != "") {
            $special = array();
            $replace = array();

            // get user roles
            $usr = AuthService::getInstance($this->w)->user();
            $roles = '';
            if (!empty($usr)) {
                foreach ($usr->getRoles() as $role) {
                    $roles .= "'" . $role . "',";
                }
                $roles = rtrim($roles, ",");
            }

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
    public function getcheckSQL($sql, PDO $connection)
    {
        // checking for rows will return false if no data is returned, even if SQL is ok
        // so let's just run the statement and try to catch any exceptions otherwise SQL runs ok
        try {
            $connection->beginTransaction();
            $rows = $connection->query($sql)->execute();
            $connection->rollBack();
            return true;
        } catch (Exception $e) {
            $connection->rollBack();
            LogService::getInstance($this->w)->error($e->getMessage());
            return false;
        }
    }

    public function getReportTemplate($id)
    {
        return $this->getObject("ReportTemplate", $id);
    }

    // build the Report navigation
    public function navigation(Web $w, $title = null, $nav = null)
    {
        if (!empty($title)) {
            $w->ctx("title", $title);
        }

        $nav = $nav ? $nav : array();

        if (AuthService::getInstance($w)->loggedIn()) {
            $w->menuLink("report/index", "Report Dashboard", $nav);

            if (AuthService::getInstance($w)->user()->hasRole("report_editor") || AuthService::getInstance($w)->user()->hasRole("report_admin")) {
                $w->menuLink("report/edit", "Create a Report", $nav);
                $w->menuLink("report-connections", "Connections", $nav);
                $w->menuLink("report/listfeed", "Feeds Dashboard", $nav);
            }
        }

        $w->ctx("navigation", $nav);
        return $nav;
    }

    public function navList(): array
    {
        $list = [
            new MenuLinkStruct("Report Dashboard", "report/index")
        ];
        if (AuthService::getInstance($this->w)->user()->hasAnyRole(["report_editor", "report_admin"])) {
            $list = [
                ...$list,
                new MenuLinkStruct("Create a Report", "report/edit"),
                new MenuLinkStruct("Connections", "report-connections"),
                new MenuLinkStruct("Feeds Dashboard", "report/listfeed"),
            ];
        }
        return $list;
    }
}
