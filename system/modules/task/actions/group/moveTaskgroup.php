<?php

use Html\Form\Autocomplete as Autocomplete;

/**
 * Action function for moving the Taskgroup, uses Angular JS
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 * @param Web $w
 */
function moveTaskGroup_GET(Web $w)
{
    list($task_id) = $w->pathMatch();

    $task = TaskService::getInstance($w)->getTask($task_id);
    $old_taskgroup = $task->getTaskGroup();

    $w->ctx("old_taskgroup", $old_taskgroup);
    $w->ctx("task", $task);
}
