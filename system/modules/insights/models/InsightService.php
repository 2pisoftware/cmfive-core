<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('PROJECT_MODULE_DIRECTORY') || define('PROJECT_MODULE_DIRECTORY', 'modules');
defined('SYSTEM_MODULE_DIRECTORY') || define('SYSTEM_MODULE_DIRECTORY', 'system' . DS . 'modules');
defined('MODELS_DIRECTORY') || define('MODELS_DIRECTORY', 'models');

class InsightService extends DbService
{
    // returns all insights reports instances
    public function GetAllInsights($module)
    {
        $availableInsights = [];

		// Read insights directory for all insights
		if ($module === 'all') {
			foreach($this->w->modules() as $insight) {
				$availableInsights += $this->getInsightsForModule($insight);
			}
		} else {
			$availableInsights = $this->getInsightsForModule($module);
		}
		
		return $availableInsights;
    }

    public function getInsightsForModule($insight)
    {
        $availableInsights = [];

        // Check insights folder
        $module_path = PROJECT_MODULE_DIRECTORY . DS . $insight . DS . MODELS_DIRECTORY;
        $system_module_path = SYSTEM_MODULE_DIRECTORY . DS . $insight . DS . MODELS_DIRECTORY;
         $insight_paths = [$module_path, $system_module_path];
         if (empty($availableInsights[$insight])) {
             $availableInsights[$insight] = [];
         }

          foreach ($insight_paths as $insight_path) {
              if (is_dir(ROOT_PATH . DS . $insight_path)) {
                  foreach (scandir(ROOT_PATH . DS . $insight_path) as $file) {
                      if (!is_dir($file) && $file{
                          0} !== '.') {
                          $classname = explode('.',$file);
                            var_dump($classname);
                            //check if file is an insight
                            //if insight add to arry. If not insight skip
                            //if (insight($classname[1])) {
                                //if ($this->isInstalled($classname[1])) {
                                    //$mig = $this->getInsightByClassname($classname[1]);
                                    //$availableInsights[$insight]
                            //Create instance of class
                            // $insightspath = $insight_path . DS . $file;
                            // if (file_exists(ROOT_PATH . DS . $insightspath)) {
                            //     include_once ROOT_PATH . DS . $insightspath;

                            //     $insight_class = preg_replace('/.php$/', '', $insight_class);
                            //     if (class_exists($insight_class)) {
                            //         $insights = (new $insight_class(1))->setWeb($this->w);
                            //         $availableInsights[$insight][$insight_path . DS . $file] = [
                            //           $insights->description(),
                            //           'pretext' => $insights->preText(),
                            //           'posttext' => $insights->postText()];
                            //     }
                            //   }
                          }
                      }
                  }
              }
                              return $availableInsights;
    }
}
