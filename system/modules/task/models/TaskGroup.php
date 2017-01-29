<?php

/**
 * 
 * A Task group defines the type of tasks which can be
 * assigned to this group, as well as the people who
 * participate in this group.
 * 
 * @author carsten
 *
 */
class TaskGroup extends DbObject {

    public $title;   // not null
    public $can_assign;  // ALL, GUEST, MEMBER, OWNER
    public $can_view;   // ALL, GUEST, MEMBER, OWNER
    public $can_create; // ALL, GUEST, MEMBER, OWNER
    public $is_active;  // 0 / 1
    public $is_deleted;  // 0 / 1
    public $description;
    public $default_assignee_id; // can not be null
    public $default_task_type;   // can be null
    public $default_priority;    // can be null
    public $task_group_type; // php class name of concrete TaskGroupType implementation
    public $_modifiable;
    
    
    public $_validation = array(
        "title" => array("required"),
        "can_assign" => array("required"),
        "can_view" => array("required"),
        "can_create" => array("required"),
        "is_active" => array("required"),
        "task_group_type" => array("required"),
    );
    
    public static $_db_table = "task_group";

    public function canList(\User $user) {
        return $this->getCanIView();
    }
    
    public function canView(\User $user) {
        return $this->getCanIView();
    }
    
    // Only owner of taskgroup or admin can edit
    public function canEdit(\User $user) {
        if ($this->Auth->user()->is_admin == 1) {
            return true;
        }
        
        return $this->isOwner($user);
    }
	
    // Only owner of taskgroup or admin can delete
    public function canDelete(\User $user) {
        if ($this->Auth->user()->is_admin == 1) {
            return true;
        }
        
        return $this->isOwner($user);
    }
	
	public function delete($force = false) {
		$tasks = $this->getTasks();
		if (!empty($tasks)) {
			foreach($tasks as $task) {
				$task->delete($force);
			}
		}
		
		parent::delete($force);
	}
    
    // get my member object. compare my role with group role required to view task group
    function getCanIView() {
        if ($this->Auth->user()->is_admin == 1) {
            return true;
        }
        
        $me = $this->Task->getMemberGroupById($this->id, $this->Auth->user()->id);
        if (empty($me)) {
            return false;
        }
        return ($this->can_view == "ALL") ? true : $this->Task->getMyPerms($me->role, $this->can_view);
    }

    // get my member object. compare my role with group role required to create tasks in this group
    function getCanICreate() {
        if ($this->Auth->user()->is_admin == 1) {
            return true;
        }
        
        $me = $this->Task->getMemberGroupById($this->id, $this->w->Auth->user()->id);
        if (empty($me)) {
            return false;
        }
        return ($this->can_create == "ALL") ? true : $this->Task->getMyPerms($me->role, $this->can_create);
    }

    // get my member object. compare my role with group role required to assign tasks in this group
    function getCanIAssign() {
        if ($this->Auth->user()->is_admin == 1) {
            return true;
        }
        
        $me = $this->Task->getMemberGroupById($this->id, $this->w->Auth->user()->id);
        if (empty($me)) {
            return false;
        }
        return ($this->can_assign == "ALL") ? true : $this->w->Task->getMyPerms($me->role, $this->can_assign);
    }

    // get task group title given task group type
    function getTypeTitle() {
        $c = $this->Task->getTaskGroupTypeObject($this->task_group_type);
        return $c ? $c->getTaskGroupTypeTitle() : null;
    }

    // get task group description given task group type
    function getTypeDescription() {
        $c = $this->Task->getTaskGroupTypeObject($this->task_group_type);
        return $c ? $c->getTaskGroupTypeDescription() : null;
    }

    // get fullname of default assignee for this task group
    function getDefaultAssigneeName() {
        $assign = $this->Auth->getUser($this->default_assignee_id);
        return $assign ? $assign->getFullName() : "";
    }

    function getTasks($where = array()) {
		$where["task_group_id"] = $this->id;
		$where["is_deleted"] = 0;
        return $this->getObjects("Task", $where);
    }

    public function getSelectOptionTitle() {
        return $this->title;
    }

    public function getSelectOptionValue() {
        return $this->id;
    }
    
    /**
     * Check if a given status is a "closing" status
     * 
     * @param unknown $status
     */
    public function isStatusClosed($status) {
    	$stats = $this->getStatus();
    	foreach ($stats as $sa) {
    		if ($sa[0]==$status) return $sa[1];
    	}
    	return false;
    }
    
    // Task replacement functions
    public function getTypes() {
        return $this->Task->getTaskTypes($this);
    }
    
    public function getTypeStatus() {
        return $this->Task->getTaskTypeStatus($this->task_group_type);
    }
    
    public function getTaskGroupTypeObject() {
        return $this->Task->getTaskGroupTypeObject($this->task_group_type);
    }
    
    public function getTaskReopen() {
        return $this->Task->getCanTaskReopen($this->task_group_type);
    }
    
    public function getStatus() {
        return $this->Task->getTaskStatus($this->task_group_type);
    }
    
    public function getPriority() {
        return $this->Task->getTaskPriority($this->task_group_type);
    }
    
    /**
     * Return true if the user is a member of the taskgroup with a role of "OWNER"
     * 
     * @param User $user
     * @return boolean
     */
    public function isOwner(User $user) {
        return null != $this->getObject("TaskGroupMember", array("task_group_id" => $this->id, "is_active" => 1, "user_id" => $user->id, "role" => "OWNER"));
    }
}
