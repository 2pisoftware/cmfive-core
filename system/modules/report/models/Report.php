<?php

class Report extends DbObject
{
    public $title;   // report title
    public $module; // module report pertains to
    public $category; // category of report given by Lookup
    public $description; // description of report
    public $report_code; // the 'code' describing the report
    public $sqltype; // determine type of statement: select/update/insert/delete
    public $is_approved; // has the Report Admin approved this report
    public $is_deleted; // is report deleted
    public $report_connection_id; // database connection object or null for default
    public $_modifiable; // employ the modifiable aspect
    public static $_db_table = "report";

    /**
     * Returns a DbPDO connection persistent for the whole requestw
     *
     * @return DbPDO|null
     */
    public function getReadOnlyDbConnection()
    {
        static $connection = null;

        if (!empty($connection)) {
            return $connection;
        }

        $config = Config::get('report.database');

        if ($config === null || empty($config['database']) || empty($config['username']) || empty($config['password'])) {
            return null;
        }

        $connection = new DbPDO($config);
        return $connection;
    }

    public function getTemplates()
    {
        return $this->getObjects("ReportTemplate", array("report_id" => $this->id, "is_deleted" => 0));
    }

    public function getMembers()
    {
        return $this->getObjects("ReportMember", ["report_id" => $this->id, "is_deleted" => 0]);
    }

    public function getOwners()
    {
        $members = $this->getMembers();
        return array_filter($members ?: [], function ($member) {
            return strtoupper($member->role) === "OWNER";
        });
    }

    public function getNumberOfOwners()
    {
        return $this->db->get("report_member")->where("report_id", $this->id)
            ->where("is_deleted", 0)
            ->where("role", "OWNER")->count();
    }

    /**
     * return the database object to call the report on.
     *
     */
    public function getDb()
    {
        if (empty($this->report_connection_id)) {
            return $this->getReadOnlyDbConnection();
        } else {
            /** @var ReportConnection */
            $dbc = $this->getObject("ReportConnection", $this->report_connection_id);
            if (!empty($dbc)) {
                return $dbc->getDb();
            }
        }
    }

    // return a category title using lookup with type: ReportCategory
    public function getCategoryTitle()
    {
        $c = ReportService::getInstance($this->w)->getObject("Lookup", array("type" => "ReportCategory", "code" => $this->category));
        if (!empty($c)) {
            return property_exists($c, "title") ? $c->title : null;
        } else {
            return null;
        }
    }

    /**
     *  build form of parameters for generating report
     */
    public function getReportCriteria()
    {
        // Determine if report is using single col form or multi col form
        // The way to do this is to find two sets of '[[' before a closing ']]'
        $is_single_col_form = true;
        $first_open_bracket_set = strpos($this->report_code, '[[');
        if ($first_open_bracket_set === false) {
            return null;
        }

        $first_closing_bracket_set = strpos($this->report_code, ']]');
        if ($first_closing_bracket_set === false) {
            return null; // Log error due to invalid report code?
        }

        $second_open_bracket_set = strpos($this->report_code, '[[', $first_open_bracket_set + 1);
        if ($second_open_bracket_set !== false && $second_open_bracket_set < $first_closing_bracket_set) {
            $is_single_col_form = false;
        }

        if ($is_single_col_form === true) {
            return $this->getSingleColFormCriteria();
        }

        $get_string = function ($string, $start, $end) {
            $string = $string;
            $ini = strpos($string, $start);
            if ($ini === false) {
                return '';
            }
            $ini += strlen($start);
            $len = strrpos($string, $end) - strlen($end);
            return substr($string, $ini, $len);
        };

        $report_code = substr($this->report_code, 0, strpos($this->report_code, '@@'));

        $report_form_layout = array_map('trim', explode('[[', $report_code));
        array_shift($report_form_layout);

        $parsed_report_form = [];
        $current_section = '';
        $current_form_row_layout = '';
        foreach ($report_form_layout as $report_form) {
            $row = array_filter(array_map('trim', explode('||', $report_form)));
            // If we have a section
            if (count($row) == 1) {
                if (!empty($current_section) && !empty($current_form_row_layout)) {
                    $parsed_report_form[$current_section][] = $this->getSingleColFormCriteria($current_form_row_layout, true);
                    $current_form_row_layout = '';
                }
                $current_section = $row[0];
                $parsed_report_form[$current_section] = [];
            } elseif (count($row) > 1) {
                foreach ($row as &$r) {
                    $has_r_bracket = strpos($r, ']]');
                    if ($has_r_bracket !== false) {
                        $r = substr($r, 0, $has_r_bracket);
                    }
                }
                $current_form_row_layout .= '[[' . implode('||', $row) . ']]';
            } else {
                if (!empty($current_section) && !empty($current_form_row_layout)) {
                    $parsed_report_form[$current_section][] = $this->getSingleColFormCriteria($current_form_row_layout, true);
                    $current_form_row_layout = '';
                }
            }
        }
        $parsed_report_form[$current_section][] = $this->getSingleColFormCriteria($current_form_row_layout, true);

        // preg_match_all("/\[\[(.*?)\|\|\s*\[\[/ms", $this->report_code, $_sections);
        // // var_dump($_sections);

        // $_form = $get_string($this->report_code, '[[', ']]');
        // $split_arr = preg_split("/\|\|/", $_form);
        // $section_name = trim(array_shift($split_arr));
        // $inner_fields = trim(implode('||', $split_arr));

        // $form = [$section_name => []];
        // // Now we need to get the rows, to do this we need to find the matching closing brackets, not the first set we find
        // preg_match_all("/\[\[\s*(\[\[.*?\]\])\s*\]\]/ms", $inner_fields, $rows);

        // if (empty($rows[1]) || !is_array($rows[1])) {
        //     return null;
        // }

        // // Get form in each row
        // foreach ($rows[1] as $form_row) {
        //     $form[$section_name][] = $this->getSingleColFormCriteria($form_row, true);
        // }

        return $parsed_report_form;
    }

    // public function getReportCriteria()
    public function getSingleColFormCriteria($data = null, $skip_section_header = false)
    {
        $data = $data ?? $this->report_code;

        // build array of all contents within any [[...]]
        preg_match_all("/\[\[.*?\]\]/", preg_replace("/\n/", " ", $data), $form);

        // if we've found elements meeting that style ....
        if ($form) {
            // foreach of the elements ...
            foreach ($form as $element) {
                // if there is actually an element ...
                if ($element) {
                    // it will be as an array so ....
                    foreach ($element as $f) {
                        // element enclosed in [[...]]. dump [[ & ]]
                        $f = preg_replace(["/\[\[\s*/", "/\s*\]\]/"], ["", ""], $f);

                        // split element on ||. rules provide for at most 4 parts in strict order
                        $name = $type = $label = $sql = null;
                        // list($name,$type,$label,$sql) = preg_split("/\|\|/", $f);
                        $split_arr = preg_split("/\|\|/", $f);
                        $name = trim(!empty($split_arr[0]) ? $split_arr[0] : '');
                        $type = trim(!empty($split_arr[1]) ? $split_arr[1] : '');
                        $label = trim(!empty($split_arr[2]) ? $split_arr[2] : '');
                        $sql = trim(!empty($split_arr[3]) ? $split_arr[3] : '');

                        if ($sql !== "") {
                            $sql = ReportService::getInstance($this->w)->putSpecialSQL($sql);
                        }

                        if (empty($arr) && !$skip_section_header) {
                            $arr = array(array("Select Report Criteria", "section"));
                        }
                        // do something different based on form element type
                        switch ($type) {
                            case "autocomplete":
                                // Fallthrough.
                            case "select":
                                if ($sql != "") {
                                    // if sql exists, check SQL is valid
                                    $flgsql = ReportService::getInstance($this->w)->getcheckSQL($sql, $this->getDb());

                                    // if valid SQL ...
                                    if ($flgsql) {
                                        //get returns for display as dropdown
                                        $values = ReportService::getInstance($this->w)->getFormDatafromSQL($sql, $this->getDb());
                                    } else {
                                        // there is a problem, say as much
                                        $values = array("SQL error");
                                    }
                                } else {
                                    // there is a problem, say as much
                                    $values = array("No SQL statement");
                                }
                                // complete array which becomes form dropdown
                                $arr[] = array($label, $type, $name, Request::string($name), $values, ($type === "autocomplete" ? 3 : null));
                                break;
                            case "checkbox":
                            case "text":
                            case "date":
                            default:
                                // complete array which becomes other form element type
                                $arr[] = array($label, $type, $name, Request::string($name));
                        }
                    }
                }
            }
        }

        // get the selection of output formats as array
        //      $format = ReportService::getInstance($this->w)->selectReportFormat();

        $templates = $this->getTemplates();
        $template_values = array();
        if (!empty($templates)) {
            foreach ($templates as $temp) {
                $template = $temp->getTemplate();
                $template_values[] = array($template->title, $temp->id);
            }
        }
        // merge arrays to give all parameter form requirements
        if (!empty($template_values)) {
            $arr[] = array("Select an Optional Template", "section");
            $arr[] = array("Format", "select", "template", null, $template_values);
        }
        // return form
        return !empty($arr) ? $arr : null;
    }

    // generate the report based on selected parameters
    public function getReportData($params = [])
    {
        // build array of all contents within any @@...@@
        //        preg_match_all("/@@[a-zA-Z0-9_\s\|,;\(\)\{\}<>\/\-='\.@:%\+\*\$]*?@@/",preg_replace("/\n/"," ",$this->report_code), $arrsql);
        preg_match_all("/@@.*?@@/", preg_replace("/\n/", " ", $this->report_code), $arrsql);

        // if we have statements, continue ...
        if ($arrsql) {
            // foreach array element ...
            foreach ($arrsql as $strsql) {
                // if element exists ....
                if ($strsql) {
                    // it will be as an array, so ...
                    foreach ($strsql as $sql) {
                        // strip our delimiters, remove newlines
                        $sql = preg_replace("/@@/", "", $sql);
                        $sql = preg_replace("/[\r\n]+/", " ", $sql);

                        // split into title and statement fields
                        list($stitle, $sql) = preg_split("/\|\|/", $sql);
                        $title = array(trim($stitle));
                        $sql = trim($sql);

                        // determine type of SQL statement, eg. select, insert, etc.
                        $sql_action = preg_split("/\s+/", $sql);
                        $action = strtolower($sql_action[0]);

                        $crumbs = array(array());
                        // each form element should correspond to a field in our SQL where clause ... substitute
                        // do not use $_REQUEST because it includes unwanted cookies
                        foreach (array_merge($params, $_GET, $_POST) as $name => $value) {
                            // convert input dates to yyyy-mm-dd for query
                            if (startsWith($name, "dt_") && !empty($value)) {
                                $value = ReportService::getInstance($this->w)->date2db($value);
                            }

                            // substitute place holder with form value
                            $sql = str_replace("{{" . $name . "}}", $value, $sql);

                            // list parameters for display
                            if (($name != SESSION_NAME) && ($name != "format")) {
                                $crumbs[0][] = $value;
                            }
                        }

                        // if our SQL is still intact ...
                        if ($sql != "") {
                            // check the SQL statement for special parameter replacements
                            $sql = ReportService::getInstance($this->w)->putSpecialSQL($sql);
                            // check the SQL statement for validity
                            $flgsql = ReportService::getInstance($this->w)->getcheckSQL($sql, $this->getDb());

                            // if valid SQL ...
                            if ($flgsql) {
                                // starter arrays
                                $hds = array();
                                $flds = array();
                                $line = array();

                                // run SQL and return recordset
                                if ($action == "select") {
                                    $rows = $this->getRowsfromSQL($sql);

                                    // if we have a recordset ...
                                    if ($rows) {
                                        // iterate ...
                                        foreach ($rows as $row) {
                                            // if row actually exists
                                            if ($row) {
                                                // foreach field/column ...
                                                foreach ($row as $name => $value) {
                                                    // build our headings array
                                                    $hds[$name] = $name;
                                                    // build a fields array
                                                    $flds[] = $value;
                                                }
                                                // put fields array into a line array and reset field array for next record
                                                $line[] = $flds;
                                                unset($flds);
                                            }
                                        }
                                        // wrap headings array appropriately
                                        $hds = array($hds);
                                        // merge to create completed report for display
                                        $tbl = array_merge($crumbs, $title, $hds, $line);

                                        $alltbl[] = $tbl;
                                        unset($line);
                                        unset($hds);
                                        unset($crumbs);
                                        unset($tbl);
                                    } else {
                                        $alltbl[] = array(array("No Data Returned for selections"), $stitle, array("Results"), array("No data returned for selections"));
                                    }
                                } else {
                                    // create headings
                                    $hds = array(array("Status", "Message"));

                                    // other SQL types do not return recordset so treat differently from SELECT
                                    try {
                                        $this->startTransaction();
                                        $rows = ReportService::getInstance($this->w)->getExefromSQL($sql, $this->getDb());
                                        $this->rollbackTransaction();
                                        $line = array(array("SUCCESS", "SQL has completed successfully"));
                                    } catch (Exception $e) {
                                        // SQL returns errors so clean up and return error
                                        $this->rollbackTransaction();
                                        LogService::getInstance($this->w)->error($e->getMessage());
                                        $line = array(array("ERROR", "A SQL error was encountered: " . $e->getMessage()));
                                    }
                                    $tbl = array_merge($crumbs, $title, $hds, $line);
                                    $alltbl[] = $tbl;
                                    unset($line);
                                    unset($hds);
                                    unset($crumbs);
                                    unset($tbl);
                                }
                            } else {
                                // if we fail the SQL check, say as much
                                $alltbl = array(array("ERROR"), array("There is a problem with your SQL statement:" . $sql));
                            }
                        } else {
                            // if we fail the SQL check, say as much
                            $alltbl = array(array("ERROR"), array("There is a problem with your SQL statement"));
                        }
                    }
                }
            }
        } else {
            $alltbl = array(array("ERROR"), array("There is a problem with your SQL statement"));
        }

        return $alltbl;
    }

    // given a report SQL statement, return recordset
    private function getRowsfromSQL($sql)
    {
        $connection = $this->getDb();

        if (!empty($connection)) {
            return $connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }

        LogService::getInstance($this->w)->error("No database connection details found for report");
        return null;
    }
}
