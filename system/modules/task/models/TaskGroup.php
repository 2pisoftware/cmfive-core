<?php

class TaskGroup extends DbObject {

    static $_DEFAULT_AUTOMATIC_SUBSCRIPTION = false;
	public $title;
	public $description;
    public $can_assign;  // ALL, GUEST, MEMBER, OWNER
    public $can_view;
    public $can_create;
    public $is_active;
    
    public $default_assignee_id;
    public $default_task_type;
    public $default_priority;
    public $task_group_type;
	public $is_automatic_subscription;
    public $_modifiable;
    
    public $_validation = [
        'title' => ['required'],
		'can_assign' => ['required'],
		'can_view' => ['required'],
		'can_create' => ['required'],
		'is_active' => ['required'],
    	'task_group_type' => ['required']
    ];

    //////////////////////////////////////
    ///REMOVE THESE
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
    //////////////



    public function getTasks() {
        return $this->getObjects('Task', ['task_group_id' => $this->id, 'is_deleted' => 0]);
    }

    public function canList(\User $user) {
        if ($this->Auth->user()->is_admin == 1) {
            return true;
        }
        
        return true;
        // $me = $this->Task->getMemberGroupById($this->id, $this->Auth->user()->id);
        // if (empty($me)) {
        //     return false;
        // }
        // return ($this->can_view == "ALL") ? true : $this->Task->getMyPerms($me->role, $this->can_view);
    }
    
    public function canView(\User $user) {
        return $this->canList($user);
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

    /**
     * Return true if the user is a member of the taskgroup with a role of "OWNER"
     * 
     * @param User $user
     * @return boolean
     */
    public function isOwner(User $user) {
        return null != $this->getObject("TaskGroupMember", array("task_group_id" => $this->id, "is_active" => 1, "user_id" => $user->id, "role" => "OWNER"));
    }

    public function printSearchTitle() {
        return $this->title;
    }

    public function printSearchUrl() {
        return '/task-group/view/' . $this->id;
    }

    public function getSelectOptionTitle() {
        return $this->title;
    }
    
    public function getTypeStatus() {
        return $this->Task->getTaskTypeStatus($this->task_group_type);
    }
    
    // get task group title given task group type
    function getTypeTitle() {
        $c = $this->Task->getTaskGroupTypeObject($this->task_group_type);
        return $c ? $c->getTaskGroupTypeTitle() : null;
    }
    
    // get fullname of default assignee for this task group
    function getDefaultAssigneeName() {
        $assign = $this->Auth->getUser($this->default_assignee_id);
        return $assign ? $assign->getFullName() : "";
    }
}