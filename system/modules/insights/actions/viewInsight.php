<?php

function viewInsight_GET(Web $w) {

    // now we need to fetch the correct insight
    // we will use pathMatch to retrieve an insight name from the url.
    [$insight_class] = $w->pathMatch("insight_class");    // $insight_class will contain whatever you put after the slash following the action name
    // if the insight name exists we will retrieve the data for that insight
    //var_dump (class_exists($insight_class));
    //var_dump (class_implements($insight_class));
    //die();

    //add a title to the action
    // change the title to reflect viewing insight
    $w->ctx('title', "View Insight for $insight_class");

    $insight = InsightService::getInstance($w)->getInsightInstance($insight_class);
    if (empty($insight)){
      $w->error('Insight does not exist', '/insights');
    }
    //var_dump($insight->getFilters($w));
    $w->ctx('filterForm',html::multiColForm($insight->getfilters($w),"/insights/runInsight/" . $insight_class, "GET", "Run"));
   
}

?>    