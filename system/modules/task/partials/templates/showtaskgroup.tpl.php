<?php if (!empty($taskgroup)) : ?>
	<table class='taskgroup_summary small-12'>
		<thead>
			<tr>
				<th colspan='2'>
					<a target="_blank" href="/task/tasklist/?task_group_id=<?php echo $taskgroup->id; ?>"><?php echo StringSanitiser::sanitise($taskgroup->title); ?></a> 
				</th>
				<th>
					<span style="float: right;"><a target="_blank" href="/task-group/viewmembergroup/<?php echo $taskgroup->id; ?>"><i class="fi-pencil"></i></a> </span>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php if (!empty($taskgroup->tasks)) : ?>
				<?php foreach($taskgroup->tasks as $task) : ?>
					<tr>
						<td width='20%'><?php echo $task->toLink(); ?></td>
						<td width='60%'><?php echo StringSanitiser::sanitise(AuthService::getInstance($w)->getUser($task->assignee_id)->getFullName()); ?></td>
						<td width='20%'><?php echo StringSanitiser::sanitise($task->status); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
<?php endif;