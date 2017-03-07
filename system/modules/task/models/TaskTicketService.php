<?php

class TaskTicketService extends DbService {
	public $taskgroup_type = "TaskGroupType_CmfiveSupport";
    public $task_type = "CmfiveTicket";
    public $taskgroup_name = "Cmfive Support Tickets";
    
    public function getTaskGroup() {
        $taskgroup = $this->w->Task->getTaskGroupTypeObject("CmfiveSupport");
        if (empty($taskgroup->id)) {
            return $this->w->Task->createTaskGroup($this->taskgroup_type, $this->taskgroup_name, "Tickets", $this->Auth->user()->id);
        }
        return $taskgroup;
    }
}