<?php
/**
 * 
 * An object of a task item attached to a checklist
 * 
 * @author Hunter de Jong hunter@2pisoftware.com
 *
 */
class TaskChecklistItem extends DbObject {
    public $object_id;          // object id
    public $checklist_id;		// id of the Checklist item
	public $title;				// Title of the checklist item

	public static $_db_table = "task_checklist_item";
}
