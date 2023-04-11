<?php

class TaskTaskAllowMemberNotificationsFix extends CmfiveMigration {

    public function up() {
        // UP
        if ($this->hasTable("task_group_notify")) {
            
            $task_groups = TaskService::getInstance($this->w)->getTaskGroups();
            foreach ($task_groups as $task_group) {
                //If this permission doesn't already exist
                if (empty($notify = TaskService::getInstance($this->w)->getTaskGroupNotifyType($task_group->id, "member", "other"))) {
                    //Create the permission to allow members to get the "other" notifications
                    $notify = new TaskGroupNotify($this->w);
                    $notify->task_group_id = $task_group->id;
                    $notify->role = "member";
                    $notify->type = "other";
                    $notify->value = 1;
                    $notify->insert();
                }
            }
		}

    }

    public function down() {
        // DOWN
        $task_groups = TaskService::getInstance($this->w)->getTaskGroups();
        if (!empty($task_groups)) {
            foreach ($task_groups as $task_group) {
                $notify = TaskService::getInstance($this->w)->getTaskGroupNotifyType($task_group->id, "member", "other");
                $notify->delete();
             }
        }
        
    }

    public function preText()
    {
        return null;
    }

    public function postText()
    {
        return "Task group members now recieve the same notifications as owners";
    }

    public function description()
    {
        return "Runs a migration that updates the already existing task groups & adds an extra row allowing members to be elligble for notification tagged with 'other'.";
    }
}
