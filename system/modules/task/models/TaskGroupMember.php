<?php
// defines task group members and their role in the group
class TaskGroupMember extends DbObject
{

    public $task_group_id;
    public $user_id;
    public $role; // OWNER, MEMBER, GUEST
    public $priority; // number to assign placement in user's list of groups
    public $is_active; // 0/1

    public static $_db_table = "task_group_member";

    public function getTaskGroup()
    {
        return $this->getObject("TaskGroup", $this->task_group_id);
    }

    /**
     * Insert override to create task subscribers when a member is added to a task group.
     * Ignores users added as a "GUEST"
     *
     * @return null
     */
    public function insert($force_validation = true)
    {
        parent::insert($force_validation);

        if ($this->role !== "GUEST") {
            $taskgroup = $this->getTaskGroup();
            if (!empty($taskgroup->id) && $taskgroup->shouldAutomaticallySubscribe()) {
                $existing_open_tasks_array = $this->w->db->get('task')->where('task_group_id', $this->task_group_id)->where('is_deleted', 0)->where('is_closed', 0)->fetchAll(); // $this->getTaskGroup()->getTasks();

                $existing_tasks = $this->fillObjects('Task', $existing_open_tasks_array);
                if (!empty($existing_tasks)) {
                    $user = AuthService::getInstance($this->w)->getUser($this->user_id);
                    foreach ($existing_tasks as $existing_task) {
                        $existing_task->addSubscriber($user);
                    }
                }
            }
        }
    }

    /**
     * Delete override to remove task subscribers when a member is removed from a task group
     *
     * @return null
     */
    public function delete($force = false)
    {
        $taskgroup = $this->getTaskGroup();
        if (!empty($taskgroup->id)) {
            $existing_tasks = $this->getTaskGroup()->getTasks();
            if (!empty($existing_tasks)) {
                foreach ($existing_tasks as $existing_task) {
                    $task_subscriber = $this->getObject('TaskSubscriber', ['task_id' => $existing_task->id, 'user_id' => $this->user_id, 'is_deleted' => 0]);
                    if (!empty($task_subscriber->id)) {
                        $task_subscriber->delete();
                    }
                }
            }
        }

        parent::delete($force);
    }
}
