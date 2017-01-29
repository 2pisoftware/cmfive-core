<?php 
echo Html::filter("Filter Tasks", $filter_data, "/task/tasklist", "GET");

if (!empty($tasks)) {
	$table_header = array("ID", "Title", "Group", "Assigned To",  "Type", "Priority", "Status", "Due");
	$table_data = array();

	// Build table data
	usort($tasks, array("TaskService", "sortTasksbyDue"));
	foreach ($tasks as $task) {
		if ($task->getCanIView()) {
			$table_line = array();
			$table_line[] = $task->id;
			$table_line[] = $task->toLink() . // Html::a("/task/edit/" . $task->id, $task->title);
				$w->partial('listTags',['object' => $task, 'limit' => 1], 'tag');

			// Append the rest of the data
			$table_line += array(null, null,
				$task->getTaskGroupTypeTitle(),
				$w->Task->getUserById($task->assignee_id),
				$task->getTypeTitle(),
				$task->priority,
				$task->status,
				$task->isTaskLate()
			);

			$table_data[] = $table_line;
		}
	}

	echo Html::table($table_data, null, "tablesorter", $table_header);
} else {
	echo '<h3><small>No tasks found.</small></h3>';
}
