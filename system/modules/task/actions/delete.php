<?php

function delete_ALL(Web &$w) {
    $p = $w->pathMatch("id");

    // task is to get updated so gather relevant data
    $task = $w->Task->getTask($p['id']);

	// if task exists, continue
    if (!empty($task->id)) {
		if (!$task->canDelete($w->Auth->user())) {
			$w->error("You aren't allowed to delete this Task", "/task/edit/" . $task->id);
			return;
		}

        $task->is_closed = 1;
        $task->is_deleted = 1;
        $task->update();
        $w->msg(__("Task: ") . $task->title . __(" has been deleted."), "/task/tasklist/");
    } else {
        $w->error(__("Task could not be found."), "/task/tasklist/");
    }
}
