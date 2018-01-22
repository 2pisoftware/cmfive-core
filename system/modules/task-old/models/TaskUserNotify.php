<?php
// defines default Task Notification Matrix as set by Task Group settings
class TaskUserNotify extends DbObject {
	public $user_id;			// user ID
	public $task_id;			// task ID
	public $task_creation;		// notify event = task creation 
	public $task_details;		// notify event = change to task details or data 
	public $task_comments;		// notify event = change to task comment 
	public $time_log;			// notify event = change to time log 
	public $task_documents;	// notify event = change to task documents 
	public $task_pages;		// notify event = change to task pages 
	
	function getDbTableName() {
		return "task_user_notify";
	}
}
