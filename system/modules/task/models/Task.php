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
    public $effort;

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

    public function isOverdue() {
        return $this->dt_due < time();
    }

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

    /**
     * Access funtions
     */
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

    /**
     * Printing functions
     */
    public function printSearchTitle() {
        return $this->title;
    }

    public function printSearchUrl() {
        return '/task/preview/' . $this->id;
    }

    public function getSelectOptionTitle() {
        return $this->printSearchTitle();
    }

    /**
     * Helper functions
     */
    public function getTimeLogEntries() {
        return $this->getObjects("timelog", ["object_class" => "Task", "object_id" => $this->id, "is_deleted" => 0]);
    }

    // return the task group title given a task group ID
    public function getTaskGroupTypeTitle() {
        return (!empty($this->_taskgroup->id) ? $this->_taskgroup->title : null);
    }

    // if i am assignee, creator or task group owner, i can set notifications for this Task
    public function getCanINotify() {
        if ($this->Auth->user()->is_admin == 1) {
            return true;
        }

        $logged_in_user_id = $this->w->Auth->user()->id;
        $me = $this->Task->getMemberGroupById($this->task_group_id, $logged_in_user_id);

        if (($logged_in_user_id == $this->assignee_id) || ($logged_in_user_id == $this->getTaskCreatorId()) || (!empty($me->role) && $this->w->Task->getMyPerms($me->role, "OWNER"))) {
            return true;
        }
        return false;
    }

    // return the ID of the task creator given a task ID
    function getTaskCreatorId() {
        $c = $this->Task->getObject("ObjectModification", array("object_id" => $this->id, "table_name" => $this->getDbTableName()));
        return $c ? $c->creator_id : "";
    }

    // return creator's name of given a task
    function getCreatorName() {
        $creator = null;
        $c = $this->Task->getObject("ObjectModification", array("object_id" => $this->id, "table_name" => $this->getDbTableName()));

        if (!empty($c->creator_id))
            $creator = $this->Auth->getUser($c->creator_id);

        return $creator ? $creator->getFullName() : "";
    }

    // return a task type object given a task type
    public function getTaskTypeObject() {
        if ($this->task_type) {
            return $this->Task->getTaskTypeObject($this->task_type);
        }
    }

    // return the task statuses as array for a task group given a task group ID
    function getTaskGroupStatus() {
        return (!empty($this->_taskgroup->id) ? $this->_taskgroup->getTypeStatus() : null);
    }

    public function getSubscribers() {
        return $this->w->db->get("task_subscriber")->select()
        ->select("task_subscriber.id, task_subscriber.user_id, concat(contact.firstname, ' ', contact.lastname) as fullname, user.is_external")
        ->where("task_subscriber.task_id", $this->id)->where('task_subscriber.is_deleted', 0)
        ->innerJoin("user on user.id = task_subscriber.user_id")
        ->innerJoin("contact on contact.id = user.contact_id")
        ->fetchAll();
    }

    public function getCreatedDate() {
        return formatDate($this->_modifiable->getCreatedDate(), "j F, Y");
    }

    public function getDueDate() {
        return formatDate($this->dt_due, "j F Y");
    }
}
