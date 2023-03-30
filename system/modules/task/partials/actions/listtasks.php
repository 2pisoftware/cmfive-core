<?php

namespace System\Modules\Task;

function listtasks(\Web $w, $params = array())
{
    $w->ctx("redirect", $params['redirect']);

    $w->ctx("hide_filter", array_key_exists('hide_filter', $params) ? $params['hide_filter'] : false);

    $taskgroup = null;
    if (!empty($params['task_group_id'])) {
        $task_group_id = $params['task_group_id'];
        $taskgroup = \TaskService::getInstance($w)->getTaskGroup($task_group_id);
    }

    if (!empty($params['task_status'])) {
        $task_status = $params['task_status'];
    }

    // Look for reset
    $reset = \Request::string("filter_reset_task_list");
    if (empty($reset)) {
        // Get filter values
        $assignee_id = \Request::int("assignee_id");
        $creator_id = \Request::int("creator_id");

        if (empty($taskgroup)) {
            $request_task_group_id = \Request::int("task_group_id");
            if (!empty($request_task_group_id)) {
                $task_group_id = $request_task_group_id;
            }
        }

        $task_type = \Request::string('task_type');
        $task_priority = \Request::string('task_priority');

        $request_task_status = \Request::string('task_status');
        if (!empty($request_task_status)) {
            $task_status = $request_task_status;
        }

        $is_closed = \Request::bool("is_closed");
        $dt_from = \Request::string('dt_from');
        $dt_to = \Request::string('dt_to');
        //$include_inactive = Request::bool('include_inactive');
    }
    // Make the query manually
    $query_object = $w->db->get("task")->leftJoin("task_group");

    // We can now make ID queries directly to the task_group table because of left join
    if (!empty($task_group_id)) {
        $query_object->where("task_group.id", $task_group_id);
    }

    // Repeat above for everything else
    if (!empty($assignee_id)) {
        $query_object->where("task.assignee_id", $assignee_id);
    }
    if (!empty($creator_id)) {
        // $query_object->where("task.creator_id", $creator_id);
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
    if (!empty($is_closed)) {
        $query_object->where("task.is_closed", ((is_null($is_closed) || $is_closed == 0) ? 0 : 1));
    } else {
        $query_object->where("task.is_closed", 0);
    }
    //do not retrieve inactive tasks
    $query_object->where("task.is_active", 1);

    // This part is why we want to make our query manually
    if (!empty($dt_from)) {
        $query_object->where("task.dt_due >= ?", $dt_from);
    }
    if (!empty($dt_to)) {
        $query_object->where("task_dt_due <= ?", $dt_to);
    }

    // Standard wheres
    $query_object->where("task.is_deleted", array(0, null))->where("task_group.is_active", 1)->where("task_group.is_deleted", 0);

    // Fetch dataset and get model objects for them
    $tasks_result_set = $query_object->fetchAll();
    $task_objects = \TaskService::getInstance($w)->getObjectsFromRows("Task", $tasks_result_set);
    $w->ctx("tasks", $task_objects);

    // Build the filter and its data
    $taskgroup_data = null;
    if (empty($taskgroup)) {
        $taskgroup_data = \TaskService::getInstance($w)->getTaskGroupDetailsForUser();
    } else {
        $taskgroup_data = \TaskService::getInstance($w)->getTaskGroupDetailsForTaskGroup($task_group_id);
    }
    $filter_data = array();
    $filter_data[] = array("Assignee", "select", "assignee_id", !empty($assignee_id) ? $assignee_id : null, $taskgroup_data["members"]);
    $filter_data[] = array("Creator", "select", "creator_id", !empty($creator_id) ? $creator_id : null, $taskgroup_data["members"]);

    if (empty($taskgroup)) {
        $filter_data[] = array("Task Group", "select", "task_group_id", !empty($task_group_id) ? $task_group_id : null, $taskgroup_data["taskgroups"]);
    } else {
        $filter_data[] = array("Task Group", "static", "taskgroupname", $taskgroup->title);
        $filter_data[] = array("", "hidden", "task_group_id", !empty($task_group_id) ? $task_group_id : null);
    }
    $filter_data[] = array("Task Type", "select", "task_type", !empty($task_type) ? $task_type : null, $taskgroup_data["types"]);
    $filter_data[] = array("Task Priority", "select", "task_priority", !empty($task_priority) ? $task_priority : null, $taskgroup_data["priorities"]);
    $filter_data[] = array("Task Status", "select", "task_status", !empty($task_status) ? $task_status : null, $taskgroup_data["statuses"]);
    $filter_data[] = array("Closed", "checkbox", "is_closed", !empty($is_closed) ? $is_closed : null);

    $w->ctx("filter_data", $filter_data);
}
