<?php 
echo (empty($hide_filter) || $hide_filter !== true) ? HtmlBootstrap5::filter("Filter Tasks", $filter_data, '/' . $redirect, "GET", "Filter", "task_list") : '';

if (!empty($tasks)) :
    $table_header = ["Title", "Created By", "Assigned To", "Group", "Type", "Priority", "Status", "Due"];
    $table_data = [];

    // Build table data
    usort($tasks, ["TaskService", "sortTasksbyDue"]);
    foreach ($tasks as $task) {
        if ($task->getCanIView()) {
            $table_line = [];
			$table_line[] = HtmlBootstrap5::a("/task/edit/" . $task->id, StringSanitiser::sanitise($task->title));

            // Append the rest of the data
            $table_line += [null,
                StringSanitiser::sanitise($task->getTaskCreatorName()),
                TaskService::getInstance($w)->getUserById($task->assignee_id),
                StringSanitiser::sanitise($task->getTaskGroupTypeTitle()),
                StringSanitiser::sanitise($task->getTypeTitle()),
                StringSanitiser::sanitise($task->priority),
                StringSanitiser::sanitise($task->status),
                $task->isTaskLate()
            ];

            $table_data[] = $table_line;
        }
    }

    echo HtmlBootstrap5::table($table_data, null, "tablesorter", $table_header);
else : ?>
    <h3><small>No tasks found.</small></h3>
<?php endif;
