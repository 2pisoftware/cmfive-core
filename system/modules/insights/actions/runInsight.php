<?php
/** @var InsightBaseClass $insight */

$run_data = $insight->run($w, $_GET);
    /** @var InsightReportInterface $data */
    foreach ($run_data as $data) {
      $w->out('<h3>' . $data->title . "</h3>");
        $w->out(Html::table($data->data, null, "tablesorter", $data->header));
    }