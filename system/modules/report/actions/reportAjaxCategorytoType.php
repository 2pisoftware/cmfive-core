<?php

// Search Filter: selecting an Category will dynamically load the Type dropdown with available values
function reportAjaxCategorytoType_ALL(Web $w) {
    $type = array();

    list($category, $module) = preg_split('/_/', Request::string('id'));

    // organise criteria
    $who = $w->session('user_id');
    $where = array();
    if (!empty($module)) {
        $where['report.module'] = $module;
    }
    if (!empty($category)) {
        $where['report.category'] = $category;
    }

    // get report categories from available report list
    $reports = ReportService::getInstance($w)->getReportsbyUserWhere($who, $where);
    if ($reports) {
        foreach ($reports as $report) {
            $arrtype = preg_split("/,/", $report->sqltype);
            foreach ($arrtype as $rtype) {
                $rtype = trim($rtype);
                if (!array_key_exists(strtolower($rtype), $type))
                    $type[strtolower($rtype)] = array(strtolower($rtype), strtolower($rtype));
            }
        }
    }
    if (empty($type)) {
        $type = array(array("No Reports", ""));
    }

    $w->setLayout(null);
    $w->out(json_encode(HtmlBootstrap5::select("type", $type)));
}