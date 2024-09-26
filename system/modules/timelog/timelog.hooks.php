<?php

function timelog_core_template_menu(Web $w)
{
    if (TimelogService::getInstance($w)->shouldShowTimer()) {
        return $w->partial('timelogwidget', null, 'timelog');
    }
}

// delete any timelogs attached to deleted object
function timelog_core_dbobject_after_delete($w, $obj)
{
    $timelogs = TimelogService::getInstance($w)->getTimelogsForObject($obj);
    if (!empty($timelogs)) {
        foreach ($timelogs as $timelog) {
            $timelog->delete();
        }
    }
}

// find the task types which have time types
function get_task_types_with_time_types($w)
{
    $taskgroup_types = TaskService::getInstance($w)->getAllTaskGroupTypes();

    $task_types = [];
    foreach ($taskgroup_types as $taskgroup_type) {
        $config_var = Config::get("task." . $taskgroup_type[1]);
        if (!$config_var || !array_key_exists('tasktypes', $config_var)) {
            continue;
        }
        
        $task_types_for_taskgroup_type = $config_var['tasktypes'];
        foreach (array_keys($task_types_for_taskgroup_type) as $task_type_for_taskgroup_type) {
            $task_types[] = 'TaskType_' . $task_type_for_taskgroup_type;
        }
    }

    $task_types_with_time_types = [];
    foreach ($task_types as $task_type) {
        $config_var = Config::get("task." . $task_type);
        if ($config_var && array_key_exists('time-types', $config_var)) {
            $task_types_with_time_types[] = substr($task_type, 9);
        }
    }
    
    return $task_types_with_time_types;
}
