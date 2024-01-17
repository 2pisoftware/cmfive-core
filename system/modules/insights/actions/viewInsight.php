<?php

/**@author Alice Hutley <alice@2pisoftware.com> */

function viewInsight_GET(Web $w)
{

    $w->setLayout('layout-bootstrap-5');

    [$insight_class] = $w->pathMatch("insight_class");    // $insight_class will contain whatever you put after the slash following the action name

    // now we need to fetch the correct insight
    // we will use pathMatch to retrieve an insight name from the url.

    // if the insight name exists we will retrieve the data for that insight
    $insight = InsightService::getInstance($w)->getInsightInstance($insight_class);
    if (empty($insight)) {
        $w->error('Insight does not exist', '/insights');
    }

    if (empty($insight->getfilters($w, $_REQUEST))) {
        $w->redirect("/insights/runInsight/" . $insight_class);
    }

    $w->ctx('title', "View Insight for " . $insight->name);

    $w->ctx('filterForm', HtmlBootstrap5::multiColForm($insight->getfilters($w, $_REQUEST), "/insights/runInsight/" . $insight_class, "GET", "Run"));
}
