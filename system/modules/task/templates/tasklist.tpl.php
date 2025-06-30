<style>
    td {
        /* hack, don't care. don't want to fight css rule precedence */
        padding: 5px 20px 5px 20px !important;
    }
</style>

<?php
echo HtmlBootstrap5::filter("Filter Tasks", $filter_data, "/task/tasklist", "GET");

if (!empty($tasks)) {
    $table_header = ["ID", "Title", "Group", "Assigned To",  "Type", "Priority", "Status", "Due"];
    $table_data = [];

    // Build table data
    // usort($tasks, array("TaskService", "sortTasksbyDue"));
    foreach ($tasks as $task) {
        if ($task->getCanIView()) {
            $table_line = [];
            $table_line[] = $task->id;
            $table_line[] = $task->toLink() . // HtmlBootstrap5::a("/task/edit/" . $task->id, $task->title);
                $w->partial('listTags', ['object' => $task, 'limit' => 1], 'tag');

            // Append the rest of the data
            $table_line += [
                null,
                null,
                $task->getTaskGroupTypeTitle(),
                TaskService::getInstance($w)->getUserById($task->assignee_id),
                $task->getTypeTitle(),
                $task->priority,
                $task->status,
                $task->isTaskLate()
            ];

            $table_data[] = $table_line;
        }
    }

    echo HtmlBootstrap5::table($table_data, null, "tablesorter", $table_header);
} else {
    echo '<h3><small>No tasks found.</small></h3>';
}