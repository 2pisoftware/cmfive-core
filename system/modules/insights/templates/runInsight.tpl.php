<?php
echo Html::b("/insights/viewInsight/" . $insight_class_name . "?" . $request_string, "Change Insight Parameters");
echo Html::b("/insights-export/csv/" . $insight_class_name . "?" . $request_string, "Export to CSV", null);
echo Html::b("#", "Export to PDF", null, 'my_pdf_button', false, null, null, null);
//echo Html::box("/insights-export/pdf/" . $insight_class_name . "?" . $request_string, "Export to PDF", false, false, null, null, "isbox", null, null, null, 'cmfive-modal');

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
<div id="insight_pdf_modal" class="reveal-modal xlarge" data-reveal>

  <!-- style="display:none; opacity:1; visibility:hidden; top:100px" aligned:position=absolute> -->
  <form id="pdf_form" class=" small-12 columns" action="<?php"/insights-export/php/" . $insight_class_name . "?" . $request_string,?>" method="POST">
    <div class="row-fluid clearfix small-12 multicolform">
      <div class="panel clearfix">
        <div class="row-fluid clearfix section-header">
          <h4>PDF Export</h4>
        </div>
        <ul class="small-block-grid-1 medium-block-grid-2 section-body">
          <li>
            <label class='small-12 columns'>
              <?php echo $template_select->label . ($template_select->required ? " <small>Required</small>" : "")
                . $template_select->__toString();
              ?>
            </label>
          </li>
        </ul>
      </div>
    </div>
    <div class="row small-12 columns">
      <button id="pdf_export_button" class="tiny button" type="submit">Export</button>
      <button id="pdf_cancel_button" class="button tiny" type="button">Cancel</button>
    </div>
  </form>
  <!--Select field goes in here along with save and cancel options. Rest of get goes in reunInsight action-->
  <a id="pdf-close-modal" class="close-reveal-modal">&#215;</a>
</div>
<!--script goes at bottom
js get element by id and send data to post function in pdf action-->
<script>
  //variable for modal and button
  let my_pdf_button = document.getElementById('my_pdf_button');
  let insight_pdf_modal = document.getElementById('insight_pdf_modal');


  //insight_pdf_modal.foundation.
  //override onclick function of button
  //onclick sets modal visibiltiy to visible
  my_pdf_button.onclick = function() {

    // insight_pdf_modal.style.display = "block";
    // insight_pdf_modal.style.visibility = "visible";
    $("#insight_pdf_modal").foundation("reveal", "open");
  }



  $("#pdf-close-modal").click(function(event) {



    console.log("hello close-modal");
    $("#insight_pdf_modal").foundation("reveal", "close");
    //insight_pdf_modal.foundation("reveal", "close");



    if ($(this).hasClass("close-reveal-modal")) {



      //new Foundation.Reveal($("#insight_pdf_modal")).close();
    } else {
      // No one is using the help system at the moment
      // Therefore no real need for a dynamic modal history
      return true;
    }
    return false;
  });



  //variable needed for save and cancel buttons
  let export_button = document.getElementById("pdf_export_button");
  let cancel_button = document.getElementById("pdf_cancel_button");



  //override onclick of cancel button. Sets visibiltiy back to hidden
  $("#pdf_cancel_button").click(function(event) {



    event.preventDefault();
    console.log('cancel modal');
    //insight_pdf_modal.style.display = "none";
    $("#insight_pdf_modal").foundation("reveal", "close");
  });


  //override onclick of save button. Send data to post funtion of PDF action (use ajax). Then close modal.


  $("#pdf_form").submit(function() {



    $("#insight_pdf_modal").foundation("reveal", "close");


    //$.ajax({

      //variable = new XMLHttpRequest();
      //send("POST");

      //url: '/insights-export/pdf'.http_build_query($_GET), //add url parameters
      //type: 'POST',
      //data: {

        //XML?
        //$w - > out(Html::multiColForm($template_list, $postUrl, 'POST', 'Save', null, null, null, '_self', true, null)),
        //$insight = InsightService::getInstance($w) - > getInsightInstance($_POST['insight_class']),
        //$run_data = $insight - > run($w, $_REQUEST),
        //$data_array = json_decode(json_encode($run_data), true),
      //},
      //complete: function(response_data) {

        //InsightService::getInstance($w)->exportpdf($run_data, $insight->name, $_POST['template_id']),
      //}
    //});


  });

  console.log('hello my_modal');
</script>