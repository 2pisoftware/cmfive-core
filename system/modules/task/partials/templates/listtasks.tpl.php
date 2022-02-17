<?php
echo (empty($hide_filter) || $hide_filter !== true) ? Html::filter("Filter Tasks", $filter_data, '/' . $redirect, "GET", "Filter", "task_list") : '';

if (!empty($tasks)) {
    $table_header = array("Title", "Created By", "Assigned To", "Group", "Type", "Priority", "Status", "Due");
    $table_data = array();
    if ($hide_columns) {
        if (is_array($columns_to_hide)) {
            $columns_to_hide = array_combine($columns_to_hide, $columns_to_hide);
            $table_header = array_diff_key($table_header, $columns_to_hide);
        } else {
            $hide_columns = false;
        }
    }

    // Build table data
    usort($tasks, array("TaskService", "sortTasksbyDue"));
    foreach ($tasks as $task) {
        if ($task->getCanIView()) {
            $table_line = array();
            $table_line[] = Html::a("/task/edit/" . $task->id, $task->title);

            // Append the rest of the data
            $table_line += array(null,
                $task->getTaskCreatorName(),
                $w->Task->getUserById($task->assignee_id),
                $task->getTaskGroupTypeTitle(),
                $task->getTypeTitle(),
                $task->priority,
                $task->status,
                $task->isTaskLate()
            );

            if ($hide_columns) {
                $table_line = array_diff_key($table_line, $columns_to_hide);
            }
            $table_data[] = $table_line;
        }
    }
    echo Html::table($table_data, null, "tablesorter", $table_header);
} else { ?>
    <h3><small>No tasks found.</small></h3>
<?php }
