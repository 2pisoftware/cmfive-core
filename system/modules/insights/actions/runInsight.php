<?php
function runInsight_GET(Web $w)
{


  /** @var InsightBaseClass $insight */
  /** this class will be used on all other classes accessed by the runInsight action. It sets up the initial parameters for each insight */

  $p = $w->pathMatch('insight_class');
  $insight = InsightService::getInstance($w)->getInsightInstance($p['insight_class']);
  $run_data = $insight->run($w, $_GET);
  /** @var InsightReportInterface $data */
  /**Defines how the insight table should look. Gives column heading for the data defined in each insight class */
  //Check for errors
  try {
  //retrieve correct insight to delete member from and redirect to
    foreach ($run_data as $data) {
      $w->out('<h3>' . $data->title . " for " . $p['insight_class'] . '</h3>');
      $w->out(Html::table($data->data, null, "tablesorter", $data->header));
    }
  } 
  //catch any fatal errors
  catch (Error $e) {
    echo "Error caught: " . $e->getMessage();
    LogService::getInstance($w)->setLogger("INSIGHTS")->error("Error occurred. Cannot run insight $p" . $e->getMessage());
  }
}
