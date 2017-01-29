<script type="text/javascript">
    var current_tab = 1;
    function switchTab(num){
        if (num == current_tab) return;
        $('#tab-'+current_tab).hide();
        $('#tab-link-'+current_tab).removeClass("active");
        $('#tab-'+num).show().addClass("active");
        $('#tab-link-'+num).addClass("active");
        current_tab = num;
    }
</script>

<div class="tabs">
	<div class="tab-head">
		<a href="/report/index?tab=1">Report Dashboard</a>
		<a href="/report/index?tab=2">Create Report</a>
		<a href="/report/index?tab=3">Approve Reports</a>
		<a id="tab-link-1" href="#" class=active onclick="switchTab(1);">View Report</a>
		<a id="tab-link-2" href="#"	onclick="switchTab(2);">Members</a>
	</div>
	<div class="tab-body">
		<div id="tab-1">
			<?php echo $btntestreport; ?>
			<p>
			<?php echo $approvereport; ?>
		</div>
		<div id="tab-2" style="display: none;">
			<?php echo $viewmembers; ?>
		</div>
	</div>
</div>

