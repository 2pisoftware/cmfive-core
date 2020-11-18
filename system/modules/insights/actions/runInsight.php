<?php
function runInsight_GET(Web $w)
{


  /** @var InsightBaseClass $insight */

  $p = $w->pathMatch('insight_class');
  $insight = InsightService::getInstance($w)->getInsightInstance($p['insight_class']);
  $run_data = $insight->run($w, $_GET);
  /** @var InsightReportInterface $data */
  //retrieve correct insight to delete member from and redirect to
  foreach ($run_data as $data) {
    $w->out('<h3>' . $data->title . " for " . $p['insight_class'] . '</h3>');
    $w->out(Html::table($data->data, null, "tablesorter", $data->header));
  }
}
