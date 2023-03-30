<?php

class TaskTicketService extends DbService {
	public $taskgroup_type = "TaskGroupType_CmfiveSupport";
    public $task_type = "CmfiveTicket";
    public $taskgroup_name = "Cmfive Support Tickets";
    
    public function getTaskGroup() {
        $taskgroup = TaskService::getInstance($this->w)->getTaskGroupTypeObject("CmfiveSupport");
        if (empty($taskgroup->id)) {
            return TaskService::getInstance($this->w)->createTaskGroup($this->taskgroup_type, $this->taskgroup_name, "Tickets", AuthService::getInstance($this->w)->user()->id);
        }
        return $taskgroup;
    }
}