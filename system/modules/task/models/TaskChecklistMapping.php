<?php
/**
 * 
 * An object linking a task and a specific task checklist
 * 
 * @author Hunter de Jong hunter@2pisoftware.com
 *
 */

class TaskChecklistMapping extends DbObject {
    public $object_id;		    // object id
    public $task_checklist_id;  // id of the task checklist
	public $task_id;			// ID of the task the checklist is attached to

	public static $_db_table = "task_checklist_item";
}




