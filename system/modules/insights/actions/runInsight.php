<?php

/**@author Alice Hutley <alice@2pisoftware.com> */

function runInsight_GET(Web $w)
{


    /** @var InsightBaseClass $insight */
    /** this class will be used on all other classes accessed by the runInsight action. It sets up the initial parameters for each insight */

  $p = $w->pathMatch('insight_class');
  $w->ctx('insight_class_name', $p['insight_class']);
  $insight = InsightService::getInstance($w)->getInsightInstance($p['insight_class']);
  $w->ctx('insight', $insight);
  $w->ctx('title', $insight->name);
  $run_data = $insight->run($w, $_GET);
  /** @var InsightReportInterface $data */
  /**Defines how the insight table should look. Gives column heading for the data defined in each insight class */
  $w->ctx('run_data', $run_data);
  
  //build request string for editing parameters
  $w->ctx('request_string', http_build_query($_GET));
}
