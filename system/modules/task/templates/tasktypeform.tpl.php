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
		<a href="/task/index">Task Dashboard</a>
		<a href="/task/tasklist">Task List</a>
		<a id="tab-link-1" href="#" class="active">Create Task</a>
	</div>
	<div class="tab-body">
		<div id="tab-1" class="clearfix">
			<?php echo $formfields; ?>
		</div>
	</div>
</div>
