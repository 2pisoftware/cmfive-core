<div class="tabs">
    <div class="tab-head">
        <a href="#report"><?php echo !empty($report->id) ? _("Edit") : _("Create"); ?> <?php echo _e('Report'); ?></a>
        <?php if (!empty($report->id)) : ?>
        	<a href="#code"><?php echo _e('SQL'); ?></a>
            <a href="#templates"><?php echo _e('Templates'); ?></a>
            <a href="#members"><?php echo _e('Members'); ?></a>
        <?php endif; ?>
        <a href="#database"><?php echo _e('View Database'); ?></a>
    </div>	
    <div class="tab-body">
        <div id="report" class="clearfix">
            <?php echo $btnrun . $report_form; ?>
        </div>
        <?php if (!empty($report->id)) : ?>
	        <div id="code" class="clearfix">
	            <?php echo $btnrun . $sql_form; ?>
	        </div>
	        <div id="templates">
	        	<p><?php echo _e('You can add special templates to render the data. Create a <a href="/admin-templates">System Template in Admin</a> and set the 
	        	module to <b>report</b>, it can then be selected here.</p>
	        	<p>The template processer uses the Twig language, you can find more information about this on
	        	the <a href="">Twig Website</a>.</p>
	        	<p>A good first step when creating a new template, is to look at the data. You can use the following
	        	twig statement in your template to do this:</p>
	        	<pre>{{dump(data)}}</pre>'); ?>
	        	<p></p>
                <?php echo Html::box("/report-templates/edit/{$report->id}", _("Add Template"), true); ?>
                <?php echo !empty($templates_table) ? $templates_table : ""; ?>
            </div>
            <div id="members" style="display: none;" class="clearfix">
                <?php echo Html::box("/report/addmembers/" . $report->id, _(" Add New Members "), true) ?>
                <?php echo $viewmembers; ?>
            </div>
        <?php endif; ?>
        <div id="database" style="display: none;" class="clearfix">
            <?php echo $dbform; ?>
        </div>
    </div>
</div>

<script language="javascript">

    $.ajaxSetup({
        cache: false
    });

    var report_url = "/report/taskAjaxSelectbyTable?id=";
    $("#dbtables").change(function() {
    	var field = $("#dbtables option:selected").val();
        $.getJSON(
                report_url + field,
                function(result) {
                    $('#dbfields').html(result);
                }
        );
    }
    );

</script>
