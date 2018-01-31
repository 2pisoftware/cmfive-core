<div class='row-fluid'>
	<div class='small-12 columns'>
		<header>Task: <span class="<?php echo $task->isOverdue() ? 'task_overdue' : ''; ?>"><?php echo $task->getSelectOptionTitle(); ?></span></header>
		<p>Created <?php echo formatDate($task->dt_created, 'd F, Y') . 
			(!empty($task->creator_id) ? ' by ' . $task->getCreator()->getFullName() : '') .
			(!empty($task->dt_due) ? ' and is due by <span class="' . ($task->isOverdue() ? 'task_overdue' : '') . '">' . formatDate($task->dt_due, 'd F, Y') . '</span>' : ''); ?></p>
	</div>
</div>

<div id="task_view_<?php echo $task->id; ?>">
	<div class='row-fluid'>
		<div class='small-12 medium-9 columns'>
			<html-tabs>
				<html-tab title='Details' icon='fa-clock' :selected="true">
					<h4 class='display-heading'>Assigned to</h4>
					<user-card :id="task.assignee_id"></user-card>
					
					<h4 class='display-heading'>Description</h4>
					<div class='display-content' v-html="task_description"></div>

					<html-button-bar>
						<button class='button tiny radius' @click="editTask()">Edit</button>
						<button class='button tiny radius secondary' @click="duplicateTask()">Duplicate</button>
						<button class='button tiny radius info' @click="newTask()">New Task</button>
						<button class='button tiny radius warning' @click="deleteTask()">Delete</button>
					</html-button-bar>
				</html-tab>
				<html-tab title='Comments' icon='fa-quote-left'>
					<?php echo $w->partial("listcomments", ["object" => $task, "internal_only" => true, "redirect" => "task/view/{$task->id}#internal_comments"], "admin"); ?>
				</html-tab>
				<html-tab title='Attachments' icon='fa-quote-right'>
					<?php echo $w->partial("listattachments", ["object" => $task, "redirect" => "task/view/{$task->id}#attachments"], "file"); ?>
				</html-tab>
				<!-- Timelog partial -->
				<!-- Internal comment partial -->
				<!-- External comment partial -->
				<!-- Attachment partial -->
			</html-tabs>
		</div>
		<div class='small-12 medium-3 columns'>
			<html-segment title='Task group details'>
				<table class='basic-table'>
					<tbody>
						<tr><td>Task group</td><td><strong><?php echo $task->getTaskGroup()->title; ?></strong></td></tr>
						<tr><td>Task type</td><td><strong><?php echo $task->task_type; ?></strong></td></tr>
						<tr><td>Description</td><td><strong><?php echo $task->getTaskGroup()->description; ?></strong></td></tr>
					</tbody>
				</table>
			</html-segment>
			<html-segment title='Subscribers'>

			</html-segment>
			<html-segment title='Tags'>
				<?php echo $w->partial('listTags', ['object' => $task], 'tag'); ?>
			</html-segment>
		</div>
	</div>
</div>

<script>

	var task_view_<?php echo $task->id; ?> = new Vue({
		el: '#task_view_<?php echo $task->id; ?>',
		data: function() {
			return {
				task: <?php echo json_encode($task->toArray()); ?>
			}
		},
		computed: {
			task_description: function() {
				return this.task.description ? this.task.description : 'No description';
			}
		}
	});

</script>