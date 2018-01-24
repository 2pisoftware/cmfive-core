<?php
class TaskDependency extends  DbObject {
	public $task_id;
	public $depends_on_task_id;
	
	public static $_db_table = "task_dependency";
}

