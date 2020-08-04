<?php

class InsightsService extends DbService
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
        $module_path = PROJECT_MODULE_DIRECTORY . DS . $insight . DS . MIGRATION_DIRECTORY;
        $system_module_path = SYSTEM_MODULE_DIRECTORY . DS . $insight . DS . MIGRATION_DIRECTORY;
        var_dump($system_module_path); die;
        $insight_paths = [$module_path, $system_module_path];
        if (empty($availableInsights[$insight])) {
            $availableInsights[$insight] = [];
        }

        // foreach ($insight_paths as $insight_path) {
        //     if (is_dir(ROOT_PATH . DS . $insight_path)) {
        //         foreach (scandir(ROOT_PATH . DS . $insight_path) as $file) {
        //             if (!is_dir($file) && $file{
        //                 0} !== '.') {
        //                 $classname = explode('.',$file);
        //                   //Create instance of class
        //                   $insightspath = $insight_path . DS . $file;
        //                   if (file_exists(ROOT_PATH . DS . $insightspath)) {
        //                       include_once ROOT_PATH . DS . $insightspath;

        //                       $insight_class = preg_replace('/.php$/', '', $insight_class);
        //                       if (class_exists($insight_class)) {
        //                           $insights = (new $insight_class(1))->setWeb($this->w);
        //                           $availableInsights[$insight][$insight_path . DS . $file] = [
        //                             $insights->description(),
        //                             'pretext' => $insights->preText(),
        //                             'posttext' => $insights->postText()];
        //                       }
        //                     }
        //                 }
        //             }
        //         }
        //     }
                              return $availableInsights;
    }
}
