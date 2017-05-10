<div class='row-fluid'>
	<div class='small-12 medium-3 columns' >
		<h4>Server OS: <?php echo $server; ?></h4>

		<?php if (!empty($load)) {
			echo '<p>Load: ' . implode(' ', $load) . '</p>';
		} ?>
	</div>
	<div class='small-12 medium-9 columns'>
		<h4>Actions</h4>
		<ul class="small-block-grid-1 medium-block-grid-2 large-block-grid-4">
			<li>
				<div class='panel action_container'>
					<button type='button' id='reindex_objects' class='button info small expand'>Reindex Searchable Objects</button>
					<p class='text-center'><span id='reindex_objects_count'><?php echo $count_indexed; ?></span> searchable objects indexed</p>
				</div>
			</li>
			<li>
				<div class='panel action_container'>
					<a id='backup_database' style='margin-bottom: 0px;' class='button info small expand' href='/admin-maintenance/ajax_backupdatabase' target='_blank'>Backup database</a>
					<p class='text-center'>DB size: <span id='backup_database_count'><?php echo $db_size; ?></span> MB</p>
				</div>
			</li>
			<li></li>
			<li></li>
			<li>
				<div class='panel action_container'>
					<a id='backup_database' style='margin-bottom: 0px;' class='button info small expand' href='/admin-maintenance/ajax_exportaudittable' target='_blank'>Export audit table to CSV</a>
					<p class='text-center' style='margin-bottom: 0px;'><?php echo $audit_row_count; ?> rows in audit table</p>
					<p class='text-center'><small>Exported Audit logs will be removed from the database</small></p>
				</div>
			</li>
			<li><!-- Your content goes here --></li>
			<li><!-- Your content goes here --></li>
			<li><!-- Your content goes here --></li>
		</ul>
		
		
	</div>
</div>

<script>
	
	$("#reindex_objects").click(function() {
		var _this = $(this);
		_this.addClass('disabled');
		$.get('/admin-maintenance/ajax_reindexobjects', function(response) {
			$("#reindex_objects_count").text(response);
			_this.removeClass('disabled');
		});
	});
	
</script>