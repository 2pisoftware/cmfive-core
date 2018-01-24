<div class="tabs">
	<div class="tab-head">
		<a href="#members">Members</a>
		<a href="#notifications">Notifications</a>
	</div>
	<div class="tab-body">
		<div id="members">
			<?php echo Html::b('/task/tasklist/?task_group_id=' . $taskgroup->id, 'Task List'); ?>
			<?php echo Html::box('/task-group/addgroupmembers/' . $taskgroup->id, 'Add New Members', true); ?>
			<?php echo Html::box($webroot . '/task-group/viewtaskgroup/' . $taskgroup->id, 'Edit Task Group', true); ?>
			<?php echo Html::box($webroot . '/task-group/deletetaskgroup/' . $taskgroup->id, 'Delete Task Group', true); ?>
			<?php echo $viewmembers; ?>

			<h4>Active Tasks</h4>
			<?php echo $w->partial('listtasks', ['task_group_id' => $taskgroup->id, 'redirect' => '/task-group/viewmembergroup/' . $taskgroup->id, 'hide_filter' => true], 'task'); ?>
		</div>
		<div id="notifications">
			<div class="row-fluid clearfix">
				<?php echo!empty($notifymatrix) ? $notifymatrix : ""; ?>
			</div>
		</div>
	</div>
</div>
