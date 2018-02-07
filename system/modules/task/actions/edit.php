<?php

use \Html\Form\InputField as InputField;
use \Html\Form\Select as Select;
use \Html\Form\Autocomplete as Autocomplete;

function edit_GET(Web $w) {

	list($task_id) = $w->pathMatch("id");
    $task = (!empty($task_id) ? $w->Task->getTask($task_id) : new Task($w));
    
    // if ($task->is_deleted == 1) {
    //     $w->error('Task has been deleted',"/task/list/");
    // }
    
    // Register for timelog if not new task
    if (!empty($task->id)) {
        $w->Timelog->registerTrackingObject($task);
    }
    
    if (!empty($task->id) && !$task->canView($w->Auth->user())) {
        $w->error("You do not have permission to edit this Task", "/task/tasklist");
    }
	
    // Get a list of the taskgroups and filter by what can be used
    $taskgroup_list = $w->Task->getTaskGroups();
    if (empty($taskgroup_list)) {
        if ((new Taskgroup($w))->canEdit($w->Auth->user())) {
            $w->msg('Please set up a taskgroup before continuing', '/task-group/viewtaskgrouptypes');
        } else {
            $w->error('There are no Tasks currently set up, please notify an Administrator', '/task');
        }
    }

    $taskgroups = array_filter($taskgroup_list, function($taskgroup){
        return $taskgroup->getCanICreate();
    });
    
    $tasktypes = array();
    $priority = array();
    $members = array();
    
    // Try and prefetch the taskgroup by given id
    $taskgroup = null;
    $taskgroup_id = $w->request("gid");
    $assigned = 0;
    if (!empty($taskgroup_id) || !empty($task->task_group_id)) {
        $taskgroup = $w->Task->getTaskGroup(!empty($task->task_group_id) ? $task->task_group_id : $taskgroup_id);
        
        if (!empty($taskgroup->id)) {
            $tasktypes = $w->Task->getTaskTypes($taskgroup->task_group_type);
            $priority = $w->Task->getTaskPriority($taskgroup->task_group_type);
            $members = $w->Task->getMembersBeAssigned($taskgroup->id);
            sort($members);
            array_unshift($members,array("Unassigned","unassigned"));
            $assigned = (empty($task->assignee_id)) ? "unassigned" : $task->assignee_id;
        }
    }

	// Create form
    $form = [
        (!empty($task_id) ? 'Edit task' : "Create a new task") => [
            [
                (new Autocomplete())
                    ->setLabel("Task Group <small>Required</small>")
                    ->setName(!empty($task_id) ? "task_group_id_text" : "task_group_id")
                    ->setReadOnly(!empty($task_id) ? 'true' : null)
                    ->setOptions($taskgroups)
                    ->setValue(!empty($taskgroup) ? $taskgroup->id : null)
                    ->setTitle(!empty($taskgroup) ? $taskgroup->getSelectOptionTitle(): null)
                    ->setRequired('required'),
                (new Select([
					"id|name" => "task_type"
				]))->setLabel("Task Type <small>Required</small>")
                    ->setDisabled(!empty($task_id) ? "true" : null)
                    ->setOptions($tasktypes)
                    ->setSelectedOption(!empty($task_id) ? $task->task_type : (is_array($tasktypes) && count($tasktypes) === 1 ? $tasktypes[0] : null))
                    ->setRequired('required')
            ],
            [
            	["Task Title", "text", "title", $task->title],
                ["Status", "select", "status", $task->status, $task->getTaskGroupStatus()],
            ],
            [
                ["Priority", "select", "priority", $task->priority, $priority],
                ["Date Due", "date", "dt_due", formatDate($task->dt_due)],
                !empty($taskgroup) && $taskgroup->getCanIAssign() ?
                	["Assigned To", "select", "assignee_id", $assigned, $members] :
                	["Assigned To", "select", "-assignee_id", $assigned, $members]
            ],
			[
				["Estimated hours", "text", "estimate_hours", $task->estimate_hours],
				["Effort", "text", "effort", $task->effort],
			],
            [["Description", "textarea", "description", $task->description]],
        	!empty($p['id']) ? [["Task Group ID", "hidden", "task_group_id", $task->task_group_id]] : null
        ]
    ];

    // Add history item
    if (empty($p['id'])) {
    	History::add("New Task");
    } else {
    	History::add("Task: {$task->title}", null, $task);
    }

    $w->ctx("task", $task);
    $w->ctx("form", Html::multiColForm($form, $w->localUrl("/task/edit/{$task->id}"), "POST", "Save", "edit_form", "prompt", null, "_self", true, Task::$_validation));
}