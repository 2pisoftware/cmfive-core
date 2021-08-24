<?php
echo Html::b("/insights/viewInsight/" . $insight_class_name . "?" . $request_string, "Change Insight Parameters");
echo Html::b("/insights-export/csv/" . $insight_class_name . "?" . $request_string, "Export to CSV", null);
echo Html::b("#", "Export to PDF", null, 'my_pdf_button', false, null, null, null);

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

<div id="insight_pdf_modal" class="reveal-modal xlarge" data-reveal>
  <form id="pdf_form" class=" small-12 columns" action="<?php echo "/insights-export/pdf/" . $insight_class_name . "?" . $request_string;?>" method="POST">

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
  <a id="pdf-close-modal" class="close-reveal-modal">&#215;</a>
</div>

<script>
  //variable for modal and button
  let my_pdf_button = document.getElementById('my_pdf_button');
  let insight_pdf_modal = document.getElementById('insight_pdf_modal');

  //override onclick function of button
  my_pdf_button.onclick = function() {
    document.getElementById("insight_pdf_modal").foundation("reveal", "open");
  }

  document.getElementById("pdf-close-modal").click(function(event)=>.getElementById("insight_pdf_modal").foundation("reveal", "close");
    if (document.getElementById(this).hasClass("close-reveal-modal")) {
    } else {
      // No one is using the help system at the moment
      // Therefore no real need for a dynamic modal history
      return true;
    }

    return false;
  );

  //set variables for save and cancel buttons
  let export_button = document.getElementById("pdf_export_button");
  let cancel_button = document.getElementById("pdf_cancel_button");

  //override onclick of cancel button. Sets visibiltiy back to hidden
  document.getElementById("pdf_cancel_button").click(function(event)=>
    event.preventDefault();
    document.getElementById("insight_pdf_modal").foundation("reveal", "close");
  );

  //override onclick of save button. Info sent to post function in HTML form. Close modal.
  document.getElementById("pdf_form").submit(function() {
    document.getElementById("insight_pdf_modal").foundation("reveal", "close");
  });
</script>