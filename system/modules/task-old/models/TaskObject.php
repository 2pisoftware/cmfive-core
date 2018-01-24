<?php
/**
 * 
 * An object attached to a specific task
 * (or a task attached to a specific object)
 * 
 * @author carsten
 *
 */
class TaskObject extends DbObject {
	public $task_id; 			// which task this object is attached to
	public $key;				// Task value reference
	public $table_name;			// DB table name of object // this is problematic!! needs class name too
	public $object_id;			// object id

	public static $_db_table = "task_object";

}
