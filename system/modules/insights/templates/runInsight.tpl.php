<?php
echo Html::b("/insights/viewInsight/" . $insight_class_name . "?" . $request_string, "Change Insight Parameters");

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



//     $arrreq = array();
//     // prepare export buttons for display if format = html
//     foreach (array_merge($_GET, $_POST) as $name => $value) {
//         $arrreq[] = $name . "=" . urlencode($value);
// $viewurl = "/insights/viewInsight/" . $p['id'] . "/?" . implode("&", $arrreq);
// $repurl = "/report/exereport/" . $p['id'] . "?";
// $strREQ = $arrreq ? implode("&", $arrreq) : "";
// $urlcsv = $repurl . $strREQ . "&format=csv";
// $btncsv = Html::b($urlcsv, "Export as CSV");
// $urlxml = $repurl . $strREQ . "&format=xml";
// $btnxml = Html::b($urlxml, "Export as XML");
// $btnpdf = Html::b($repurl . $strREQ . "&format=pdf", "Export as PDF");
