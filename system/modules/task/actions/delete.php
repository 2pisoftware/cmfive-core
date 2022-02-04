<?php

function delete_ALL(Web &$w) {
    $p = $w->pathMatch("id");

    // task is to get updated so gather relevant data
    $task = TaskService::getInstance($w)->getTask($p['id']);

	// if task exists, continue
    if (!empty($task->id)) {
		if (!$task->canDelete(AuthService::getInstance($w)->user())) {
			$w->error("You aren't allowed to delete this Task", "/task/edit/" . $task->id);
			return;
		}

        //$task->is_closed = 1;
        //$task->is_deleted = 1;
        //$task->update();
        $task->delete();
        $w->msg("Task: " . $task->title . " has been deleted.", "/task/tasklist/");
    } else {
        $w->error("Task could not be found.", "/task/tasklist/");
    }
}
