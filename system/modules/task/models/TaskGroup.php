<?php

class TaskGroup extends DbObject {

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