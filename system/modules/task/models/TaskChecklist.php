<?php

/**
 * 
 * An object of a task Checklist
 * 
 * 
 * @author Hunter de Jong hunter@2pisoftware.com
 *
 */
class TaskChecklist extends DbObject
{
    public $object_id;            // object id
    public $title;                // Title of the checklist
    public $is_template;          //Signifies if the checlist is a template

    public static $_db_table = "task_checklist";
}
