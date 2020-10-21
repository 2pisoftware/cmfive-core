<?php

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
            foreach ($this->w->modules() as $insight) {
                $availableInsights += $this->getInsightsForModule($insight);
            }
        } else {
            $availableInsights = $this->getInsightsForModule($insights);
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
        if (empty($availableInsights[$module])) {
            $availableInsights[$module] = [];
        }

        foreach ($insight_paths as $insight_path) {
            if (is_dir(ROOT_PATH . DS . $insight_path)) {
                foreach (scandir(ROOT_PATH . DS . $insight_path) as $file) {
                    if (!is_dir($file) && $file{
                        0} !== '.') {
                        $classname = explode('.', $file);
                        //var_dump($classname);
                        //check if file is an insight
                        //if insight add to arry. If not insight skip
                        if (strpos($classname[0], 'Insight') !== false && $classname[0] !== "InsightBaseClass" && $classname[0] !== "InsightService") {
                            //echo "Found insights class; " . $classname[0] . " <br>";
                            //Create instance of class
                            $insightspath = $insight_path . DS . $file;
                            if (file_exists(ROOT_PATH . DS . $insightspath)) {
                                include_once ROOT_PATH . DS . $insightspath;
                                if (class_exists($classname[0])) {
                                    $insight = new $classname[0]($this->w);
                                    //is_subclass_of ( mixed $object , string $class_name [, bool $allow_string = TRUE ] ) : bool
                                    if (is_subclass_of($insight, 'InsightBaseClass')){
                                        $availableInsights[$module][] = $insight;
                                    }
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
    public function getAllMembersForInsightClass($classname = null){
        if (empty($classname)){
            $this->w->error('No insight class name provided');
        }
        return $this->getObjects('InsightMembers', ['is_deleted'=>0,'insight_class_name'=>$classname]);
    }
     
    //Checking users mebership against insight
    public function getUserMembershipForInsight($classname = null, $user_id = null){
        if (empty($classname)){
            $this->w->error('No insight class name provided');
        }
        if  (empty($user_id)){
            $this->w->error('No user provided');
        }
        $insight_member = $this->getObject('InsightMembers',['is_deleted' => 0,'insight_class_name' => $classname, 'user_id' => $user_id]);
        return $insight_member->type;
    }

    // static list of group permissions
    public function getInsightPermissions()
    {
        return array("OWNER", "MEMBER");
    }

    //returns single insight member matching the given id
    public function getMemberForId() {
        return $this->GetObject('insightMember',$id);
    }

    //Check if user is memebr of an insight class
    public function isMember() {
        $insight_class = $w->request('insight_class');
        foreach ($user_id) {
            if ($insight_class === 'insight_class_name') {
                return true;
            }
        }
    }
}
