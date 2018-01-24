<?php
// defines default Task Group Notification Matrix as set by OWNER
class TaskGroupNotify extends DbObject {
	public $task_group_id;	// task group ID
	public $role;			// member role: guest|membr|owner
	public $type;			// notify type: creator|assignee|all others
	public $value;			// flag: 0|1
	
	public static $_db_table = "task_group_notify";

}

