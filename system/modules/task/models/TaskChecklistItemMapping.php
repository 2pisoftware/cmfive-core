<?php
/**
 * 
 * An object linking a checklist item with a specific task
 * Also shows wether it has been checked
 * 
 * @author Hunter de Jong hunter@2pisoftware.com
 *
 */
class TaskChecklistMapping extends DbObject {
    public $object_id;		    // object id
    public $task_checklist_item_id;  // id of the task checklist item
	public $task_id;			// ID of the task the checklist item is attached to
    public $is_checked;         // Wether or not the item is checked

    //public $table_name;			// DB table name of object // this is problematic!! needs class name too

	public static $_db_table = "task_checklist_item";
}
