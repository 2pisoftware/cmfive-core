<?php

/**
 * This action is responsible for migrating data from TaskTime to Timelog
 * 
 * @param Web $w
 */
function migrate_GET(Web $w) {
	
	$lock_file = "system/modules/timelog/install/MIGRATION.LOCK";
	
	if (!file_exists($lock_file)) {
		
		$tasktimes = TaskService::getInstance($w)->getObjects("TaskTime", ["is_deleted" => 0]);
		if (!empty($tasktimes)) {
			foreach($tasktimes as $tasktime) {
				$task = $tasktime->getTask();
				$comment = $tasktime->getComment();
				
				$timelog = new Timelog($w);
				$timelog->object_class = "Task";
				$timelog->object_id = $task->id;
				$timelog->user_id = $tasktime->user_id;
				$timelog->time_type = $tasktime->time_type;
				$timelog->dt_start = $tasktime->dt_start;
				$timelog->dt_end = $tasktime->dt_end;
				$timelog->is_suspect = $tasktime->is_suspect;
				$timelog->insert();
				
				// Need to preserve created timestamp for ordering
				$timelog->dt_created = $tasktime->dt_created;
				$timelog->update();
				
				if (!empty($comment->comment)) {
					$timelog->setComment($comment->comment);
				}
			}
		}
		
		$w->out("Timelog successfully migrated.<br/>" . Html::a("/timelog", "View Timelog"));
		file_put_contents($lock_file, "");
	} else {
		$w->out("Timelog migration cannot run, lock is in place.<br/>" . Html::a("/timelog", "View Timelog"));
	}
}