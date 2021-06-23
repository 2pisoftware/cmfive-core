<?php

/**@author Alice Hutley <alice@2pisoftware.com> */

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('PROJECT_MODULE_DIRECTORY') || define('PROJECT_MODULE_DIRECTORY', 'modules');
defined('SYSTEM_MODULE_DIRECTORY') || define('SYSTEM_MODULE_DIRECTORY', 'system' . DS . 'modules');
defined('MODELS_DIRECTORY') || define('MODELS_DIRECTORY', 'models');

class InsightService extends DbService
{
    // returns all insight instances
    public function getAllInsights($insights)
    {
        $availableInsights = [];

        // Read module directory for all insights
        if ($insights === 'all') {
            foreach ($this->w->modules() as $module) {
                $availableInsights[$module] = $this->getInsightsForModule($module);
            }
        } else {
            $availableInsights[$insights] = $this->getInsightsForModule($insights);
        }

        return $availableInsights;
    }

    // Find Insight models in each folder
    public function getInsightsForModule($module)
    {
        $availableInsights = [];

        // Check modules folder
        $module_path = PROJECT_MODULE_DIRECTORY . DS . $module . DS . MODELS_DIRECTORY;
        $system_module_path = SYSTEM_MODULE_DIRECTORY . DS . $module . DS . MODELS_DIRECTORY;
        $insight_paths = [$module_path, $system_module_path];
        // Check if module contains file with Insight in the name
        foreach ($insight_paths as $insight_path) {
            if (is_dir(ROOT_PATH . DS . $insight_path)) {
                foreach (scandir(ROOT_PATH . DS . $insight_path) as $file) {
                    if (!is_dir($file) && $file[0] !== '.') {
                        $classname = explode('.', $file);
                        //check if file is an insight
                        //if insight add to arry. If not insight skip
                        if (strpos($classname[0], 'Insight') !== false && $classname[0] !== "InsightBaseClass" && $classname[0] !== "InsightService") {
                            //Create instance of class
                            $insightspath = $insight_path . DS . $file;
                            if (file_exists(ROOT_PATH . DS . $insightspath)) {
                                include_once ROOT_PATH . DS . $insightspath;
                                if (class_exists($classname[0]) && is_subclass_of($classname[0], 'InsightBaseClass')) {
                                    $insight = new $classname[0]($this->w);
                                    $availableInsights[] = $insight;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $availableInsights;
    }

    // Create string for Insight model
    public function getStringContainingInsight($classname)
    {
        //Return files with name Insight
        return $this->getObject('Insight', ['classname' => $classname]);
    }

    //Members service functions
    //finding memebers for a specific insight
    public function getAllMembersForInsightClass($classname = null)
    {
        if (empty($classname)) {
            $this->w->error('No insight class name provided');
        }
        return $this->getObjects('InsightMembers', ['is_deleted' => 0, 'insight_class_name' => $classname]);
    }

    //Checking users mebership against insight
    public function getUserMembershipForInsight($classname = null, $user_id = null)
    {
        if (empty($classname)) {
            $this->w->error('No insight class name provided');
        }
        if (empty($user_id)) {
            $this->w->error('No user provided');
        }
        $insight_member = $this->getObject('InsightMembers', ['is_deleted' => 0, 'insight_class_name' => $classname, 'user_id' => $user_id]);
        if (empty($insight_member)) {
            return null;
        }
        return $insight_member->type;
    }

    // static list of group permissions
    public function getInsightPermissions()
    {
        return ["OWNER", "MEMBER"];
    }

    //check if user is a member of an insight
    public function IsMember($insight_class_name, $user_id)
    {

        if (AuthService::getInstance($this->w)->getUser($user_id)->hasRole('insights_admin')) {
            return true;
        }
        $member = $this->getObject('InsightMembers', ['is_deleted' => 0, 'insight_class_name' => $insight_class_name, 'user_id' => $user_id]);
        if (empty($member)) {
            return false;
        }
        return true;
    }

    //retrieve a specific member matching the id given number
    public function GetMemberForId($id)
    {
        return $this->GetObject('InsightMembers', $id);
    }

    public function getInsightInstance(string $insight_class)
    {
        if (!empty($insight_class) && class_exists($insight_class) && is_subclass_of($insight_class, "InsightBaseClass")) {
            return new $insight_class();
        }
        return null;
    }

    public function isInsightOwner($user_id, $insight_class)
    {
        if (AuthService::getInstance($this->w)->getUser($user_id)->hasRole('insights_admin')) {
            return true;
        }
        if (InsightService::getInstance($this->w)->getUserMembershipForInsight($insight_class, $user_id) == "OWNER") {
            return true;
        }
    }

    // convert dd/mm/yyyy date to yyyy-mm-dd for SQL statements
    public function date2db($date)
    {
        if ($date) {
            $formatteddate = formatDate($date, 'Y-m-d');
            return $formatteddate;
        }
    }

    // export a recordset as CSV
    public function exportcsv($run_data, $title)
    {
        // set filename
        $filename = str_replace(" ", "_", $title) . "_" . date("Y.m.d-H.i") . ".csv";
        foreach ($run_data as $table) {
            if (!empty($table)) {
                $title = $table->title;
                $hds = [];
                foreach ($table->header as $hd) {
                    $hds[$hd] = $hd;
                }
                $csv = new ParseCsv\Csv();
                $csv->output_filename = $filename;
                // ignore lib wrapper csv->output, to keep control over header re-sends!
                
                $this->w->out($csv->unparse($table->data, $hds, null, null, null));
                // can't use this way without commenting out header section, which composer won't like
            }
        }

        $this->w->sendHeader("Content-type", "application/csv");
        $this->w->sendHeader("Content-Disposition", "attachment; filename=" . $filename);
        $this->w->setLayout(null);
    }
    
    // export a recordset as PDF
    public function exportpdf($run_data, $title, $report_template_id = null)
    {
            $filename = str_replace(" ", "_", $title) . "_" . date("Y.m.d-H.i") . ".pdf";
            //var_dump($filename); die;
            // using TCPDF, but sourcing from Composer
            //require_once('tcpdf/tcpdf.php');
    
            // instantiate and set parameters
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle($title);
            $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
            $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            //$pdf->
            //var_dump($title); die;
            //$pdf->setLanguageArray($l);
            // no header, set font and create a page
            $pdf->setPrintHeader(false);
            $pdf->SetFont("helvetica", "B", 9);
            $pdf->AddPage();
        
    
           
 
            
        ////////////
        if (!empty($run_data)) {
            if (empty($report_template_id)) {
                // title of report
                $hd = "<h1>" . $title . "</h1>";
                $pdf->writeHTMLCell(0, 10, 60, 15, $hd, 0, 1, 0, true);
                $created = date("d/m/Y g:i a");
                $pdf->writeHTMLCell(0, 10, 60, 25, $created, 0, 1, 0, true);
            //var_dump($created); die;
            // display recordset
                foreach ($run_data as $table) {
                    if (!empty($table)) {
                        $title = $table->title;
                        $hds = [];
                        foreach ($table->header as $hd) {
                            $hds[] = $hd;
                        }
                    // foreach ($rows as $row) {
                    //     //throw away the first line which list the form parameters
                    //     $crumbs = array_shift($row);
                    //     $title = array_shift($row);
                    //     $hds = array_shift($row);
                    //     $hds = array_values($hds);
                        //var_dump($hds); die;
    
                        $results = "<h3>" . $title . "</h3>";
                        $results .= "<table cellpadding='2' cellspacing='2' border='0' width='50px'>\n";
                        //$pdf->writeHTML($results, true, false, true, false, '');
                        foreach ($table->data as $row) {
                            $rowresults = '';
                            //var_dump($row); die;
                            $i = 0;
                            foreach ($row as $field) {
                                if (!stripos($hds[$i], "_link")) {
                                    $results .= "<tr><td width='5px'>" . $hds[$i] . "</td><td>" . $field . "</td></tr>\n";
                                }
                                $i++;
                            }
                            $results .= "<tr><td colspan=2></td></tr>\n";
                            //echo $results; die;
                            //$pdf->writeHTML($results, true, false, true, false, '');
                        }
                        $results .= "</table><p>";

                        //var_dump($results); die;
                        //echo $results; die;
                        $pdf->writeHTML($results, true, false, true, false, '');
                    }
                }
            } else {
                $templatedata = [];
                $templatedata ["insightTitle"] = $title;
                $templatedata ["tables"] = [];
                foreach ($run_data as $table) {
                    if (!empty($table)) {
                        $title = $table->title;
                        $hds = [];
                        foreach ($table->header as $hd) {
                            $hds[] = $hd;
                        }
                        // $data = [];
                        // foreach ($table->data as $row) {
                        //     var_dump($d);
                        //     $data[$r] = $d;
                        // }
                    //var_dump($data); die;
                        // $crumbs = array_shift($row);
                        // $title = array_shift($row);
                        // $hds = array_shift($row);
                        // $hds = array_values($hds);
    
                        $templatedata["tables"][] = ["title" => $title, "headers" => $hd, "results" => $table->data];
                    //var_dump($title); die;
    
                        if (!empty($report_template_id) && !empty($templatedata)) {
                            $results = $this->w->Template->render(
                                $report_template_id,
                                ["data" => $templatedata, "w" => $this->w, "POST" => $_POST]
                            );
    
                            $pdf->writeHTML($results, true, false, true, false, '');
                        }
                    }
                }
            }
            // set for 'open/save as...' dialog
            $pdf->Output($filename, 'D');

            //echo 'test service';

            // $pdf->lastPage();
             return $pdf;
        }
    }
}
