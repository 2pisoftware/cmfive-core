<?php
echo Html::b("/insights/viewInsight/" . $insight_class_name . "?" . $request_string, "Change Insight Parameters");
echo Html::b("/insights-export/csv/" . $insight_class_name . "?" . $request_string, "Export to CSV", null);
//echo Html::b("#", "Export to PDF", null, $pdf, false, null, null, null);
echo Html::box("/insights-export/pdf/" . $insight_class_name . "?" . $request_string, "Export to PDF", false, false, null, null, "isbox", null, null, null, 'cmfive-modal');

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
?>
<!--div GET item on to become visible id($pdf)-->
<div id="cmfive-modal" class="reveal-modal xlarge open" data-reveal=""  style="display:block; opacity:1; visibility:hidden; top:100px" aligned:position=absolute>
  <form class=" small-12 columns">
    <div class="row-fluid clearfix small-12 multicolform">
      <div class="panel clearfix">
        <div class="row-fluid clearfix section-header"></div>
        <ul class="small-block-grid-1 medium-block-grid-2 section-body">
          <li>
            <label class="small-12 columns">
              <select style="visibility:visible"></select>
            </label>
          </li>
        </ul>
      </div>
    </div>
    <div class="row small-12 columns">
      <button class="button tiny tiny button savebutton"></button>
      <button class="button tiny tiny button cancelbutton"></button>
    </div>
  </form>
</div>
<!--Select field goes in here along with save and cancel options. Rest of get goes in reunInsight action-->
</div>
<!--script goes at bottom
js get elemt by id and send data to post function in pdf action-->