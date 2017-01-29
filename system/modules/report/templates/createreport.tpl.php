<div class="tabs">
    <div class="tab-head">
        <a href="#tab-1">Create Report</a>
        <a href="#tab-2">View Database</a>
    </div>
    <div class="tab-body">
        <div id="tab-1">
            <p>Please review the <b>Help</b> file for full instructions on the special syntax used to create reports.</p>
            <div class="clearfix">
                <?php echo $createreport; ?>
            </div>
        </div>
        <div id="tab-2" style="display: none;">
            <div class="clearfix">
                <?php echo $dbform; ?>
            </div>
        </div>
   </div>
</div>

<script>
    $.ajaxSetup ({
        cache: false
    });

    var report_url = "/report/taskAjaxSelectbyTable?id="; 
    $("select[id='dbtables']").change(function(event) {
        $.getJSON(report_url + $(this).val(), function(result) {
            $('#dbfields').closest(".small-12").html("<span id='dbfields'>" + result + "</span>");
        });
    });
</script>
