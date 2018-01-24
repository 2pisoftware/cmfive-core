<?php
/**
 * Abstract class describing types of
 * Tasks
 *
 */
abstract class TaskType {
	public $w;
	
	function __construct(Web $w) {
		$this->w = $w;
	}
	function getTaskTypeTitle(){
		return Config::get("task.".get_class($this).".title");
	}
	
	function getTaskTypeDescription() {
		return Config::get("task.".get_class($this).".description");
	}
	
	/**
	 * return a value that should be added to the search index for this task
	 */
	function addToIndex(Task $task) {}
	
	/**
	 * return an array similar to the Html::form
	 * which describes the fields available for this
	 * task type and the way they should be presented in
	 * task details.
	 * 
	 */
	function getFieldFormArray(TaskGroup $taskgroup, Task $task = null) {}
	
	/**
	 * Executed before a task is inserted into DB
	 * 
	 * @param Task $task
	 */
	function on_before_insert(Task $task) {
		if (!empty($task)) {
			$task->w->callHook("task", get_class($this)."_on_before_insert", $task);
		}
	}	
	/**
	 * Executed after a task has been inserted into DB
	 * 
	 * @param Task $task
	 */
	function on_after_insert(Task $task) {
		if (!empty($task)) {
			$task->w->callHook("task", get_class($this)."_on_after_insert", $task);
		}
	}	
	/**
	 * Executed before a task is updated in the DB
	 * 
	 * @param Task $task
	 */
	function on_before_update(Task $task) {
		if (!empty($task)) {
			$task->w->callHook("task", get_class($this)."_on_before_update", $task);
		}
	}	
	/**
	 * Executed after a task has been updated in the DB
	 * 
	 * @param Task $task
	 */
	function on_after_update(Task $task) {
		if (!empty($task)) {
			$task->w->callHook("task", get_class($this)."_on_after_update", $task);
		}
	}	
	/**
	 * Executed before a task is deleted from the DB
	 * 
	 * @param Task $task
	 */
	function on_before_delete(Task $task) {
		if (!empty($task)) {
			$task->w->callHook("task", get_class($this)."_on_before_delete", $task);
		}
	}	
	/**
	 * Executed after a task has been deleted from the DB
	 * 
	 * @param Task $task
	 */
	function on_after_delete(Task $task) {
		if (!empty($task)) {
			$task->w->callHook("task", get_class($this)."_on_after_delete", $task);
		}
	}
	/**
	 * Return a html string which will be displayed alongside
	 * the generic task details.
	 * 
	 * @deprecated this should move out into a hook called from the task template!
	 * @param Task $task
	 */
	function displayExtraDetails(Task $task) {}
	
	/**
	 * Return a Html string which will be appended to the row of buttons in the viewtask screen.
	 * 
	 * @deprecated this should move out into a hook called from the task template!
	 * @param Task $task
	 */
	function displayExtraButtons(Task $task) {}
	
	/**
	 * Return an array of options for time types
	 * - override in subclass
	 * - or specify in config.php using key 'task.<subclassname>.time-types'
	 * 
	 * @return array
	 */
	function getTimeTypes() {
		return Config::get("task.".get_class($this).".time-types");
	} 

}
