<?php namespace System\Modules\Task;

function listtaskgroups(\Web $w, $params = array()) {
    $taskgroups = $params['taskgroups'];
    
    $should_filter = !empty($params['should_filter']) ? $params['should_filter'] : false;
    $filter_closed_tasks = !empty($params['filter_closed_tasks']) ? $params['filter_closed_tasks'] : false;
    
    if ($should_filter) {
    	
        $taskgroups = array_filter($taskgroups, function($taskgroup) use ($w,$filter_closed_tasks) {
            // First check if there are tasks
            $tasks = $taskgroup->getTasks();
            if (count($tasks) == 0) {
                return false;
            } else {

                // Check if any of the tasks are accessible to the user
                $tasks = array_filter($tasks, function($task) use ($w,$filter_closed_tasks) {
                	if ($filter_closed_tasks && $task->isStatusClosed()) {
                		return false;
                	} else {
                    	return $task->getCanIView();
                	}
                });

                // If there are tasks that the user can view then show the taskgroup
                return (count($tasks) > 0);
            }

        });
    }
    
    $w->ctx("taskgroups", $taskgroups);
    $w->ctx("redirect", $params['redirect']);
}
