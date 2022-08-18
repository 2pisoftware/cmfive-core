<?php

class TaskChecklistService extends DbService
{
    public function getTaskChecklists($id) 
    {
        return $this->getObjects("TaskChecklistMapping", ["task_checklist_id" => $id]);
    }

    public function getTaskChecklist($id)
    {
        return $this->getObject("TaskChecklist", $id);
    }

    // get the task data from the database given a task ID
    public function getTaskChecklistData($id)
    {
        return $this->getObjects("TaskChecklistItem", ["checklist_id" => $id]);
    }
}

?>