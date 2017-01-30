<?php

function tasklist_ALL(Web $w) {
	History::add("List Tasks");
	$w->ctx("title", "Task List");
	
    // Look for reset
    $reset = $w->request("reset");
	$is_closed = 0;
    if (empty($reset)) {
        // Get filter values
        $assignee_id = $w->sessionOrRequest("task__assignee-id");
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
		$taskgroup = $w->Task->getTaskGroup($task_group_id);
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
    
    // Standard wheres
    $query_object->where("task.is_deleted", array(0, null)); //->where("task_group.is_active", 1)->where("task_group.is_deleted", 0);

	// Fetch dataset and get model objects for them
    $tasks_result_set = $query_object->fetch_all();
    $task_objects = $w->Task->getObjectsFromRows("Task", $tasks_result_set);
    
	// Filter in or out closed tasks based on given is_closed filter parameter
	if (!empty($task_objects) && empty($reset)) {
		$task_objects = array_filter($task_objects, function($task) use ($is_closed, $filter_urgent) {
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
    $taskgroup_data = $w->Task->getTaskGroupDetailsForUser();
    $filter_assignees = $taskgroup_data["members"];
    array_unshift($filter_assignees,array(__("Unassigned"),"unassigned"));
    $filter_data = array(
        array("Assignee", "select", "task__assignee-id", !empty($assignee_id) ? $assignee_id : null, $filter_assignees),
        array("Creator", "select", "task__creator-id", !empty($creator_id) ? $creator_id : null, $taskgroup_data["members"]),
        (new \Html\Form\Autocomplete([
			"label"		=> "Task Group",
			"name"		=> "task__task-group-id",
			"id"		=> "task__task-group-id",
			"source"	=> $w->localUrl("/task-group/ajaxAutocompleteTaskgroups"),
			"value"		=> !empty($task_group_id) ? $taskgroup->getSelectOptionValue() : null,
			"minlength" => 2,
			"title"		=> !empty($task_group_id) ? $taskgroup->getSelectOptionTitle() : null
		])), // array("Task Group", "select", "task__task-group-id", !empty($task_group_id) ? $task_group_id : null, $taskgroup_data["taskgroups"]),
        array("Task Type", "select", "task__type", !empty($task_type) ? $task_type : null, $taskgroup_data["types"]),
        array("Task Priority", "select", "task__priority", !empty($filter_urgent) ? "Urgent" : !empty($task_priority) ? $task_priority : null, $taskgroup_data["priorities"]),
        array("Task Status", "select", "task__status", !empty($task_status) ? $task_status : null, $taskgroup_data["statuses"]),
        (new \Html\Form\Select([
			"label"		=> "Closed",
			"name"		=> "task__is-closed",
			"id"		=> "task__is_closed"
		]))->setOptions([
			["label" => "No", "value" => '0'],
			["label" => "Yes", "value" => '1'],
			["label" => "Both", "value" => '']
		])->setSelectedOption($is_closed) //array("Closed", "checkbox", "task__is-closed", !empty($is_closed) ? $is_closed : null)
    );
    
    $w->ctx("filter_data", $filter_data);
    
    
    // tab: notifications
    // list groups and notification based on my role and permissions
    $line = array(array(__("Task Group"), __("Your Role"), __("Creator"), __("Assignee"), __("All Others"), ""));
    $user_taskgroup_members = $w->Task->getMemberGroups($w->Auth->user()->id);
    if ($user_taskgroup_members) {
        usort($user_taskgroup_members, array("TaskService", "sortbyRole"));

        foreach ($user_taskgroup_members as $member) {
            $taskgroup = $member->getTaskGroup();
            $value_array = array();
            $notify = $w->Task->getTaskGroupUserNotify($w->Auth->user()->id, $member->task_group_id);
            if ($notify) {
                foreach ($notify as $n) {
                    $value = ($n->value == "0") ? __("No") : __("Yes");
                    $value_array[$n->role][$n->type] = $value;
                }
            } else {
                $notify = $w->Task->getTaskGroupNotify($member->task_group_id);
                if ($notify) {
                    foreach ($notify as $n) {
                        $value = ($n->value == "0") ? __("No") : __("Yes");
                        $value_array[$n->role][$n->type] = $value;
                    }
                }
            }

            if ($taskgroup->getCanIView()) {
                $title = $w->Task->getTaskGroupTitleById($member->task_group_id);
                $role = strtolower($member->role);

                $line[] = array(
                    $title,
                    ucfirst($role),
                    @$value_array[$role]["creator"],
                    @$value_array[$role]["assignee"],
                    @$value_array[$role]["other"],
                    Html::box(WEBROOT . "/task/updateusergroupnotify/" . $member->task_group_id, __(" Edit "), true)
                );
            }
            unset($value_array);
        }
        

        // display list
        $w->ctx("notify", Html::table($line, null, "tablesorter", true));
    }
}
