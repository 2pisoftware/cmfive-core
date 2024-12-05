<?php
use Html\Form\Html5Autocomplete;
use Html\Form\Select;

function tasklist_ALL(Web $w)
{
    History::add("List Tasks");
    $w->ctx("title", "Task List");

    // Look for reset
    $reset = Request::string("reset");
    $is_closed = 0;
    if (empty($reset)) {
        // Get filter values
        $assignee_id = $w->sessionOrRequest("task__assignee-id", AuthService::getInstance($w)->user()->id);
        $creator_id = $w->sessionOrRequest("task__creator-id");

        $task_group_id = $w->sessionOrRequest("task__task-group-id");
        $task_type = $w->sessionOrRequest('task__type');
        $task_priority = $w->sessionOrRequest('task__priority');
        $task_status = $w->sessionOrRequest('task__status');
        $is_closed = $w->sessionOrRequest("task__is-closed", 0);
        $dt_from = $w->sessionOrRequest('task__dt-from');
        $dt_to = $w->sessionOrRequest('task__dt-to');
        $filter_urgent = $w->sessionOrRequest('task__filter-urgent', false);
    }

    // First get the taskgroup
    $taskgroup = null;
    if (!empty($task_group_id)) {
        $taskgroup = TaskService::getInstance($w)->getTaskGroup($task_group_id);
    }

    // Make the query manually
    $query_object = $w->db->get("task")->leftJoin("task_group")->where("task_group.is_deleted", 0);

    // We can now make ID queries directly to the task_group table because of left join
    if (!empty($task_group_id)) {
        $query_object->where("task.task_group_id", $task_group_id);
    }

    // Repeat above for everything else
    if (!empty($assignee_id)) {
        // Unassigned has a value of 'unassigned' in filter but 0 in db
        if ($assignee_id == 'unassigned') {
            $query_object->where("task.assignee_id", 0);
        } else {
            $query_object->where("task.assignee_id", $assignee_id);
        }
    }
    if (!empty($creator_id)) {
        $query_object->leftJoin("object_modification on object_modification.object_id = task.id and object_modification.table_name = 'task'")
            ->where("object_modification.creator_id", $creator_id);
    }
    if (!empty($task_type)) {
        $query_object->where("task.task_type", $task_type);
    }
    if (!empty($task_priority)) {
        $query_object->where("task.priority", $task_priority);
    }
    if (!empty($task_status)) {
        $query_object->where("task.status", $task_status);
    }
    //    if (!empty($is_closed)) {
    //        $query_object->where("task.is_closed", ((is_null($is_closed) || $is_closed == 0) ? 0 : 1));
    //    } else {
    //        $query_object->where("task.is_closed", 0);
    //    }
    // This part is why we want to make our query manually
    if (!empty($dt_from)) {
        if ($dt_from == "NULL") {
            $query_object->where("task.dt_due", null);
        } else {
            $query_object->where("task.dt_due >= ?", $dt_from);
        }
    }
    if (!empty($dt_to)) {
        if ($dt_to == "NULL") {
            $query_object->where("task.dt_due", null);
        } else {
            $query_object->where("task.dt_due <= ?", $dt_to);
        }
    }
    $query_object->where("task.is_active", 1);



    // Standard wheres
    $query_object->where("task.is_deleted", [0, null]); //->where("task_group.is_active", 1)->where("task_group.is_deleted", 0);

    // Fetch dataset and get model objects for them
    $tasks_result_set = $query_object->orderBy('task.id DESC')->fetchAll();
    $task_objects = TaskService::getInstance($w)->getObjectsFromRows("Task", $tasks_result_set);

    // Filter in or out closed tasks based on given is_closed filter parameter
    if (!empty($task_objects) && empty($reset)) {
        $task_objects = array_filter($task_objects, function ($task) use ($is_closed, $filter_urgent) {
            if (!is_null($filter_urgent) && $filter_urgent == '1') {
                if (is_null($is_closed) || $is_closed === '') {
                    return $task->isUrgent();
                } else {
                    return $task->isUrgent() && ($is_closed == '0' ? !$task->getisTaskClosed() : $task->getisTaskClosed());
                }
            }

            if (is_null($is_closed) || $is_closed === '') {
                return true;
            }

            return ($is_closed == '0' ? !$task->getisTaskClosed() : $task->getisTaskClosed());
        });
    }

    $w->ctx("tasks", $task_objects);

    // Build the filter and its data
    $taskgroup_data = TaskService::getInstance($w)->getTaskGroupDetailsForUser();
    $filter_assignees = $taskgroup_data["members"];
    array_unshift($filter_assignees, ["Unassigned", "unassigned"]);
    $filter_data = [

        new Select([
            "name|id" => "task__assignee-id",
            "label" => "Assignee",
            "selected_option" => !empty($assignee_id) ? $assignee_id : null,
            "options" => $filter_assignees,
        ]),

        new Select([
            "name|id" => "task__creator-id",
            "label" => "Creator",
            "selected_option" => !empty($creator_id) ? $creator_id : null,
            "options" => $taskgroup_data["members"],
        ]),

        (new Html5Autocomplete([
            "id|name" => "task__task-group-id",
            "label" => "Task Group",
            "placeholder" => "Search",
            "value" => !empty($task_group_id) ? $taskgroup->getSelectOptionValue() : null,
            "source" => $w->localUrl("/task-group/ajaxAutocompleteTaskgroups"),
            "minLength" => 2,
        ])),

        new Select([
            "name|id" => "task__type",
            "label" => "Task Type",
            "selected_option" => !empty($task_type) ? $task_type : null,
            "options" => $taskgroup_data["types"],
        ]),

        new Select([
            "name|id" => "task__priority",
            "label" => "Task Priority",
            "selected_option" => !empty($filter_urgent) ? "Urgent" : (!empty($task_priority) ? $task_priority : null),
            "options" => $taskgroup_data["priorities"],
        ]),

        new Select([
            "name|id" => "task__status",
            "label" => "Task Status",
            "selected_option" => !empty($task_status) ? $task_status : null,
            "options" => $taskgroup_data["statuses"],
        ]),

        (new Select([
            "label"        => "Closed",
            "name"        => "task__is-closed",
            "id"        => "task__is_closed",
            "options" => [
                ["label" => "No", "value" => '0'],
                ["label" => "Yes", "value" => '1'],
                ["label" => "Both", "value" => '']
            ],
            "selected_option" => $is_closed,
        ]))
    ];

    $w->ctx("filter_data", $filter_data);
}
