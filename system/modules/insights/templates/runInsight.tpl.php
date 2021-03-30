<?php
echo Html::b("/insights/viewInsight/" . $insight_class_name . "?" . $request_string, "Change Insight Parameters");
//echo Html::box("/insights-export/csv/" . $insight_class_name . "?" . $request_string, "Export to CSV", true);

//Check for errors
try {
    //retrieve correct insight to delete member from and redirect to
      foreach ($run_data as $data) {
        echo '<h4>' . $data->title . '</h4>';
        echo Html::table($data->data, null, "tablesorter", $data->header);
      }
    } 
    //catch any fatal errors
    catch (Error $e) {
      echo "Error caught: " . $e->getMessage();
      LogService::getInstance($w)->setLogger("INSIGHTS")->error("Error occurred. Cannot run insight $p" . $e->getMessage());
    }
    

