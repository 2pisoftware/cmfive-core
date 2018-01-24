<?php
// defines default Task Group Notification Matrix as set by OWNER
class TaskGroupUserNotify extends DbObject {
	public $user_id;			// user ID
	public $task_group_id;		// task group ID
	public $role;				// member role: guest|membr|owner
	public $type;				// notify type: creator|assignee|all others
	public $value;				// flag: 0|1
	public $task_creation;		// notify event = task creation 
	public $task_details;		// notify event = change to task details or data 
	public $task_comments;		// notify event = change to task comment 
	public $time_log;			// notify event = change to time log 
	public $task_documents;	// notify event = change to task documents 
	public $task_pages;		// notify event = change to task pages 

	public static $_db_table = "task_group_user_notify";

}
