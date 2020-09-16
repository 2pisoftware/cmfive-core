<?php

function viewInsight_GET(Web $w) {
    // now we need to fetch the correct insight
    // we will use pathMatch to retrieve an insight name from the url.
    [$insight_class] = $w->pathMatch("insight_class");    // $insight_class will contain whatever you put after the slash following the action name
    // if the insight name exists we will retrieve the data for that insight
    //var_dump (class_exists($insight_class));
    //var_dump (class_implements($insight_class));
    //die();
    if (empty($insight_class)||!class_exists($insight_class) || !is_subclass_of($insight_class, "InsightBaseClass")) {
      $w->error('Insight does not exist', '/insights');
    }

    //add a title to the action
    // change the title to reflect viewing insight
    $w->ctx('title', 'View Insight');
}
?>    