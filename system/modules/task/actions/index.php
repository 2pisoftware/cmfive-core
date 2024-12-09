<?php

//////////////////////////////////////////
//          TASK DASHBOARD   		//
//////////////////////////////////////////

function index_ALL(Web $w)
{
    TaskService::getInstance($w)->navigation($w, "Task Dashboard");

    // I want to see:
    //   Number of open tasks assigned to me (out of total open tasks) \/
    //   My Tasks that are overdue (with tasks with no due date)
    //   My tasks with urgent status
    //   Taskgroups that I'm a member of and the amount of tasks in it

    $total_tasks = $w->db->get("task")->where("is_deleted", 0)->count();
    $task_rows = $w->db->get("task")->leftJoin("task_group")
        ->where("task.assignee_id", AuthService::getInstance($w)->user()->id)
        ->where("task.is_deleted", array(0, null))
        ->where("task_group.is_active", 1)
        ->where("task_group.is_deleted", 0)
        ->fetchAll();
    $tasks = !empty($task_rows) ? TaskService::getInstance($w)->getObjectsFromRows('Task', $task_rows) : [];

    $taskgroups = TaskService::getInstance($w)->getTaskGroupsForMember(AuthService::getInstance($w)->user()->id);

    $count_overdue = 0;
    $count_due_soon = 0;
    $count_no_due_date = 0;
    $count_todo_urgent = 0;
    $count_taskgroup_tasks = 0;

    // Task group task count
    if (!empty($taskgroups)) {
        foreach ($taskgroups as $taskgroup) {
            $count_taskgroup_tasks += count($taskgroup->getTasks());
        }
    }

    // Task breakdown
    if (!empty($tasks)) {
        // Strip out tasks that are already done
        $tasks = array_filter($tasks, function ($task) {
            return !$task->getisTaskClosed();
        });

        if (!empty($tasks)) {
            foreach ($tasks as $task) {
                if (!empty($task->dt_due) && ($task->dt_due < time())) {
                    $count_overdue++;
                } else if (!empty($task->dt_due) && ($task->dt2time($task->dt_due) <= (time() + (60 * 60 * 24 * 7)))) {
                    $count_due_soon++;
                } else if (empty($task->dt_due)) {
                    $count_no_due_date++;
                }
                if ($task->isUrgent()) {
                    $count_todo_urgent++;
                }
            }
        }
    }

    $w->ctx("taskgroups", !empty($taskgroups) ? $taskgroups : []);
    $w->ctx("tasks", !empty($tasks) ? $tasks : []);
    $w->ctx("total_tasks", $total_tasks);
    $w->ctx("count_overdue", $count_overdue);
    $w->ctx("count_due_soon", $count_due_soon);
    $w->ctx("count_no_due_date", $count_no_due_date);
    $w->ctx("count_todo_urgent", $count_todo_urgent);
    $w->ctx("count_taskgroup_tasks", $count_taskgroup_tasks);
}
