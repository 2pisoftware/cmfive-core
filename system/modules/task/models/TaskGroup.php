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
class TaskGroup extends DbObject
{
    static $_DEFAULT_AUTOMATIC_SUBSCRIPTION = false;

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
    public $is_automatic_subscription;
    public $_modifiable;


    public static $_validation = [
        "title" => ["required"],
        "can_assign" => ["required"],
        "can_view" => ["required"],
        "can_create" => ["required"],
        "is_active" => ["required"],
        "task_group_type" => ["required"],
    ];

    public static $_db_table = "task_group";

    /**
     * To ensure task_group_notify objects are also copied, set $saveToDb to true
     *
     * @param boolean $saveToDb default false
     * @return TaskGroup
     */
    public function copy($saveToDb = false)
    {
        $new_taskgroup = parent::copy($saveToDb);

        if (!!$saveToDb) {
            foreach ($this->getTaskGroupNotify() ?: [] as $notify) {
                $new_notify = $notify->copy(false);
                $new_notify->task_group_id = $new_taskgroup->id;
                $new_notify->insert();
            }
        } else {
            LogService::getInstance($this->w)->setLogger('TASK')->warn('$saveToDb is false, skipping copy of task group notify objects');
        }

        return $new_taskgroup;
    }

    public function getTaskGroupNotify()
    {
        return $this->getObjects('TaskGroupNotify', ['task_group_id' => $this->id]);
    }

    public function getMembers()
    {
        return $this->getObjects("TaskGroupMember", ['task_group_id' => $this->id]);
    }

    public function shouldAutomaticallySubscribe()
    {
        return !!$this->is_automatic_subscription;
    }

    public function getUnclosedTasks()
    {
        return $this->getObjects("Task", ['task_group_id' => $this->id, 'is_deleted' => 0, 'is_closed' => 0]);
    }

    public function getTasks()
    {
        return $this->getObjects("Task", ['task_group_id' => $this->id, 'is_deleted' => 0]);
    }

    public function canList(\User $user)
    {
        return $this->getCanIView();
    }

    public function canView(\User $user)
    {
        return $this->getCanIView();
    }

    // Only owner of taskgroup or admin can edit
    public function canEdit(\User $user)
    {
        if (AuthService::getInstance($this->w)->user()->is_admin == 1) {
            return true;
        }

        return $this->isOwner($user);
    }

    // Only owner of taskgroup or admin can delete
    public function canDelete(\User $user)
    {
        if (AuthService::getInstance($this->w)->user()->is_admin == 1) {
            return true;
        }

        return $this->isOwner($user);
    }

    public function delete($force = false)
    {
        $tasks = $this->getTasks();
        if (!empty($tasks)) {
            foreach ($tasks as $task) {
                $task->delete($force);
            }
        }

        parent::delete($force);
    }

    // get my member object. compare my role with group role required to view task group
    public function getCanIView()
    {
        if (AuthService::getInstance($this->w)->user()->is_admin == 1) {
            return true;
        }

        $me = TaskService::getInstance($this->w)->getMemberGroupById($this->id, AuthService::getInstance($this->w)->user()->id);
        if (empty($me)) {
            return false;
        }
        return ($this->can_view == "ALL") ? true : TaskService::getInstance($this->w)->getMyPerms($me->role, $this->can_view);
    }

    // get my member object. compare my role with group role required to create tasks in this group
    public function getCanICreate()
    {
        if (AuthService::getInstance($this->w)->user()->is_admin == 1) {
            return true;
        }

        $me = TaskService::getInstance($this->w)->getMemberGroupById($this->id, AuthService::getInstance($this->w)->user()->id);
        if (empty($me)) {
            return false;
        }
        return ($this->can_create == "ALL") ? true : TaskService::getInstance($this->w)->getMyPerms($me->role, $this->can_create);
    }

    // get my member object. compare my role with group role required to assign tasks in this group
    public function getCanIAssign()
    {
        if (AuthService::getInstance($this->w)->user()->is_admin == 1) {
            return true;
        }

        $me = TaskService::getInstance($this->w)->getMemberGroupById($this->id, AuthService::getInstance($this->w)->user()->id);
        if (empty($me)) {
            return false;
        }
        return ($this->can_assign == "ALL") ? true : TaskService::getInstance($this->w)->getMyPerms($me->role, $this->can_assign);
    }

    // get task group title given task group type
    public function getTypeTitle()
    {
        $c = TaskService::getInstance($this->w)->getTaskGroupTypeObject($this->task_group_type);
        return $c ? $c->getTaskGroupTypeTitle() : null;
    }

    // get task group description given task group type
    public function getTypeDescription()
    {
        $c = TaskService::getInstance($this->w)->getTaskGroupTypeObject($this->task_group_type);
        return $c ? $c->getTaskGroupTypeDescription() : null;
    }

    // get fullname of default assignee for this task group
    public function getDefaultAssigneeName()
    {
        $assign = AuthService::getInstance($this->w)->getUser($this->default_assignee_id);
        return $assign ? $assign->getFullName() : "";
    }

    public function getSelectOptionTitle()
    {
        return $this->title;
    }

    public function getSelectOptionValue()
    {
        return $this->id;
    }

    /**
     * Check if a given status is a "closing" status
     *
     * @param unknown $status
     */
    public function isStatusClosed($status)
    {
        $stats = $this->getStatus();
        foreach ($stats as $sa) {
            if ($sa[0] == $status) {
                return $sa[1];
            }
        }
        return false;
    }

    // Task replacement functions
    public function getTypes()
    {
        return TaskService::getInstance($this->w)->getTaskTypes($this);
    }

    public function getTypeStatus()
    {
        return TaskService::getInstance($this->w)->getTaskTypeStatus($this->task_group_type);
    }

    public function getTaskGroupTypeObject()
    {
        return TaskService::getInstance($this->w)->getTaskGroupTypeObject($this->task_group_type);
    }

    public function getTaskReopen()
    {
        return TaskService::getInstance($this->w)->getCanTaskReopen($this->task_group_type);
    }

    public function getStatus()
    {
        return TaskService::getInstance($this->w)->getTaskStatus($this->task_group_type);
    }

    public function getPriority()
    {
        return TaskService::getInstance($this->w)->getTaskPriority($this->task_group_type);
    }

    /**
     * Return true if the user is a member of the taskgroup with a role of "OWNER"
     *
     * @param User $user
     * @return boolean
     */
    public function isOwner(User $user)
    {
        return null != $this->getObject("TaskGroupMember", array("task_group_id" => $this->id, "is_active" => 1, "user_id" => $user->id, "role" => "OWNER"));
    }
}
