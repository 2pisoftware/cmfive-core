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
        return array("OWNER", "MEMBER");
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
                foreach ($table->header as $hd){
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
}