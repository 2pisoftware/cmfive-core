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
<div id="insight_pdf_modal" class="reveal-modal xlarge open" data-reveal=""  style="display:none; opacity:1; visibility:hidden; top:100px" aligned:position=absolute>
  <form class=" small-12 columns">
    <div class="row-fluid clearfix small-12 multicolform">
      <div class="panel clearfix">
        <div class="row-fluid clearfix section-header"></div>
        <ul class="small-block-grid-1 medium-block-grid-2 section-body">
          <li>
            <?php
              echo $template_select->__toString();
            ?>
          </li>
        </ul>
      </div>
    </div>
    <div class="row small-12 columns">
      <button id="save_button" class="button tiny tiny button savebutton"></button>
      <button id="cancel_button" class="button tiny tiny button cancelbutton"></button>
    </div>
  </form>
<!--Select field goes in here along with save and cancel options. Rest of get goes in reunInsight action-->
</div>
<!--script goes at bottom
js get element by id and send data to post function in pdf action-->
<script>
  //variable for modal and button
  let my_pdf_button = document.getElementById('my_pdf_button')
  let insight_pdf_modal = document.getElementById('insight_pdf_modal')
  //override onclick function of button
  //onclick sets modal visibiltiy to visible
  my_pdf_button.onclick = function(){
    insight_pdf_modal.style.display = "block"
    insight_pdf_modal.style.visibility = "visible"
  }
  //variable needed for save and cancel buttons
  let save_button = document.getElementById("save_button")
  let cancel_button = document.getElementById("cancel_button")
  //override onclick of cancel button. Sets visibiltiy back to hidden
  cancel_button.onclick = function(){
    insight_pdf_modal.style.display = "none"
  }
  //override onclick of save button. Send data to post funtion of PDF action (use ajax). Then close modal.
  //close when click outside of modal
  window.onclick = function(e){
    if(e.target == insight_pdf_modal){
      insight_pdf_modal.style.display = "none"
    }
  }
  console.log('hello my_modal');
</script>