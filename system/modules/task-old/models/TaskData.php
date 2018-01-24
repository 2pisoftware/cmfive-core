<?php
/**
 * Store the data of a single task
 * @author carsten
 *
 */
class TaskData extends DbObject {
	public $task_id;
	public $data_key;
	public $value;	
	
	public function addToIndex() {
		return $this->value;
	}
	
	public function getTask() {
		return $this->getObject("Task", $this->task_id);
	}
	
	public function insertOrUpdate($force_null_values = false, $force_validation = true) {
		parent::insertOrUpdate($force_null_values, $force_validation);
		
		$task = $this->getTask();
		$task->_searchable->update();
	}
	
	public function insert($force_validation = true) {
		parent::insert($force_validation);
		
		$task = $this->getTask();
		$task->_searchable->update();
	}
	
	public function update($force_null_values = false, $force_validation = true) {
		parent::update($force_null_values, $force_validation);
		
		$task = $this->getTask();
		$task->_searchable->update();
	}
	
}
