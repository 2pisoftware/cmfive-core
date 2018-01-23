<?php

class Task extends DbObject {

	public $parent_id;
	public $task_group_id;

	public $task_type;
    public $title;
    public $description;
    
    public $status;
    public $priority;

	public $rate;
    public $estimate_hours;

    public $assignee_id;
    public $dt_assigned;

    public $dt_completed;
    public $is_closed;
    public $dt_due;
    
    public $_modifiable;
    public $_searchable;
    
    public static $_validation = [
        "title" => ['required'],
        "task_group_id" => ['required'],
        "status" => ['required'],
        "task_type" => ['required']
    ];

    /**
     * Returns attached task group
     * 
     * @return TaskGroup
     */
    public function getTaskGroup() {
    	return $this->getObject('TaskGroup', $this->task_group_id);
    }

    /**
     * Returns assigned user
     * 
     * @return User
     */
    public function getAssignee() {
    	return $this->getObject('User', $this->assignee_id);
    }

    /**
     * Returns parent task, if any
     * 
     * @return Task
     */
    public function getParentTask() {
    	return $this->getObject('Task', $this->parent_id);
    }


    // Task access
    public function canList(User $user) {
		return true;
    }

    public function canView(User $user) {
    	return true;
    }

    public function canEdit(User $user) {
    	return true;
    }

    public function canDelete(User $user) {
    	return true;
    }

}