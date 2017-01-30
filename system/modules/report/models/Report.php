<?php

class Report extends DbObject {

    public $title;   // report title
    public $module; // module report pertains to
    public $category;   // category of report given by Lookup
    public $description; // description of report
    public $report_code;  // the 'code' describing the report
    public $sqltype;  // determine type of statement: select/update/insert/delete
    public $is_approved; // has the Report Admin approved this report
    public $is_deleted; // is report deleted
    public $report_connection_id; // database connection object or null for default
    public $_modifiable; // employ the modifiable aspect
    public static $_db_table = "report";

    public function getTemplates() {
        return $this->getObjects("ReportTemplate", array("report_id" => $this->id, "is_deleted" => 0));
    }
	
	public function getMembers() {
		return $this->getObjects("ReportMember", ["report_id" => $this->id, "is_deleted" => 0]);
	}
    
	public function getOwners() {
		$members = $this->getMembers();
		return array_filter($members ? : [], function($member) {
			return strtoupper($member->role) === "OWNER";
		});
	}
	
	public function getNumberOfOwners() {
		return $this->db->get("report_member")->where("report_id", $this->id)
				->where("is_deleted", 0)
				->where("role", "OWNER")->count();
	}
	
    /**
     * return the database object to call the report on.
     * 
     */
    function getDb() {
        if (empty($this->report_connection_id)) {
            return $this->_db;
        } else {
            $dbc = $this->getObject("ReportConnection", $this->report_connection_id);
            if (!empty($dbc)) {
                return $dbc->getDb();
            }
        }
    }

    // return a category title using lookup with type: ReportCategory
    function getCategoryTitle() {
        $c = $this->Report->getObject("Lookup", array("type" => "ReportCategory", "code" => $this->category));
        if (!empty($c)) {
            return property_exists($c, "title") ? $c->title : null;
        } else {
            return null;
        }
    }

    /**
     *  build form of parameters for generating report
     */
    function getReportCriteria() {
    	
        // build array of all contents within any [[...]]
        preg_match_all("/\[\[.*?\]\]/", preg_replace("/\n/", " ", $this->report_code), $form);

        // if we've found elements meeting that style ....
        if ($form) {
            // foreach of the elements ...
            foreach ($form as $element) {
                // if there is actually an element ...
                if ($element) {
                    // it will be as an array so ....
                    foreach ($element as $f) {
                        // element enclosed in [[...]]. dump [[ & ]]
                        $patterns = array();
                        $patterns[0] = "/\[\[\s*/";
                        $patterns[1] = "/\s*\]\]/";
                        $replacements = array();
                        $replacements[0] = "";
                        $replacements[1] = "";
                        $f = preg_replace($patterns, $replacements, $f);

                        // split element on ||. rules provide for at most 4 parts in strict order
                        $name = $type = $label = $sql = null;
                        // list($name,$type,$label,$sql) = preg_split("/\|\|/", $f);
                        $split_arr = preg_split("/\|\|/", $f);
                        $name = trim(!empty($split_arr[0]) ? $split_arr[0] : '');
                        $type = trim(!empty($split_arr[1]) ? $split_arr[1] : '');
                        $label = trim(!empty($split_arr[2]) ? $split_arr[2] : '');
                        $sql = trim(!empty($split_arr[3]) ? $split_arr[3] : '');

                        if ($sql !== "") {
                            $sql = $this->Report->putSpecialSQL($sql);
                        }

                        if (empty($arr)) {
                        	$arr = array(array("Select Report Criteria", "section"));
                        }
                        // do something different based on form element type
                        switch ($type) {
                            case "autocomplete":
                                $minValue = 3;
                            case "select":
                                if ($sql != "") {
                                    // if sql exists, check SQL is valid
                                    $flgsql = $this->Report->getcheckSQL($sql, $this->getDb());

                                    // if valid SQL ...
                                    if ($flgsql) {
                                        //get returns for display as dropdown
                                        $values = $this->Report->getFormDatafromSQL($sql, $this->getDb());
                                    } else {
                                        // there is a problem, say as much
                                        $values = array("SQL error");
                                    }
                                } else {
                                    // there is a problem, say as much
                                    $values = array("No SQL statement");
                                }
                                // complete array which becomes form dropdown
                                $arr[] = array($label, $type, $name, $this->w->request($name), $values, ($type === "autocomplete" ? $minValue : null));
                                break;
                            case "checkbox":
                            case "text":
                            case "date":
                            default:
                                // complete array which becomes other form element type
                                $arr[] = array($label, $type, $name, $this->w->request($name));
                        }
                    }
                }
            }
        }
        
        // get the selection of output formats as array
//      $format = $this->Report->selectReportFormat();
        
        $templates = $this->getTemplates();
        $template_values = array();
        if (!empty($templates)) {
            foreach($templates as $temp) {
                $template = $temp->getTemplate(); 
                $template_values[] = array($template->title, $temp->id);
            }
        }
        // merge arrays to give all parameter form requirements
        if (!empty($template_values)) {
        	$arr[] = array(__("Select an Optional Template"), "section");
        	$arr[] =array(__("Format"), "select", "template", null, $template_values);
        }
        // return form
        return !empty($arr) ? $arr : null;
    }

    // generate the report based on selected parameters
    function getReportData($user_id=null) {
        // build array of all contents within any @@...@@
        //		preg_match_all("/@@[a-zA-Z0-9_\s\|,;\(\)\{\}<>\/\-='\.@:%\+\*\$]*?@@/",preg_replace("/\n/"," ",$this->report_code), $arrsql);
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
                        $arrsql = preg_split("/\s+/", $sql);
                        $action = strtolower($arrsql[0]);

                        $crumbs = array(array());
                        // each form element should correspond to a field in our SQL where clause ... substitute
                        // do not use $_REQUEST because it includes unwanted cookies
                        foreach (array_merge($_GET, $_POST) as $name => $value) {
                            // convert input dates to yyyy-mm-dd for query
                            if (startsWith($name, "dt_"))
                                $value = $this->Report->date2db($value);

                            // substitute place holder with form value
                            $sql = str_replace("{{" . $name . "}}", $value, $sql);

                            // list parameters for display
                            if (($name != SESSION_NAME) && ($name != "format"))
                                $crumbs[0][] = $value;
                        }

                        // if our SQL is still intact ...
                        if ($sql != "") {
                            // check the SQL statement for special parameter replacements
                            $sql = $this->Report->putSpecialSQL($sql,$user_id);
                            // check the SQL statement for validity
                            $flgsql = $this->Report->getcheckSQL($sql, $this->getDb());
                            
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
                                        $alltbl[] = array(array(__("No Data Returned for selections")), $stitle, array(__("Results")), array(__("No data returned for selections")));
                                    }
                                } else {
                                    // create headings
                                    $hds = array(array(__("Status"), __("Message")));

                                    // other SQL types do not return recordset so treat differently from SELECT
                                    try {
                                        $this->startTransaction();
                                        $rows = $this->Report->getExefromSQL($sql, $this->getDb());
                                        $this->commitTransaction();
                                        $line = array(array("SUCCESS", __("SQL has completed successfully")));
                                    } catch (Exception $e) {
                                        // SQL returns errors so clean up and return error
                                        $this->rollbackTransaction();
                                        $line = array(array("ERROR", __("A SQL error was encountered: ") . $e->getMessage()));
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
                                $alltbl = array(array("ERROR"), array(__("There is a problem with your SQL statement").":" . $sql));
                            }
                        } else {
                            // if we fail the SQL check, say as much
                            $alltbl = array(array("ERROR"), array(__("There is a problem with your SQL statement")));
                        }
                    }
                }
            }
        } else {
            $alltbl = array(array("ERROR"), array(__("There is a problem with your SQL statement")));
        }
        
        return $alltbl;
    }

    // given a report SQL statement, return recordset
    private function getRowsfromSQL($sql) {
        if (!empty($this->report_connection_id)) {
            $connection = $this->getDb();
            $return = $connection->query($sql)->fetchAll();
        } else {
            $return = $this->_db->sql($sql)->fetch_all(PDO::FETCH_BOTH);
        }
        
        if (!empty($return)) {
            foreach ($return as $key => $val) {
                foreach ($val as $k => $v) {
                    if (is_int($k)) {
                        unset($return[$key][$k]);
                    }
                }
            }
        }
        
        return $return;
    }

}
