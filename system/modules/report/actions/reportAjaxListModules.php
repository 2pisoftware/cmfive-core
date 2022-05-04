<?php
// Search Filter: load relevnt Module dropdown available values
function reportAjaxListModules_ALL(Web $w) {
	$modules = array();

	// organise criteria
	$who = $w->session('user_id');
	$where = "";

	// get report categories from available report list
	$reports = ReportService::getInstance($w)->getReportsbyUserWhere($who, $where);
	if ($reports) {
		foreach ($reports as $report) {
			if (!array_key_exists($report->module, $modules))
			$modules[$report->module] = array(ucfirst($report->module),$report->module);
		}
	}
	if (!$modules)
	$modules = array(array("No Reports",""));

	// load Module dropdown and return
	$modules = Html::select("module",$modules);

	$w->setLayout(null);
	$w->out(json_encode($modules));
}
