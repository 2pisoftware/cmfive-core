<?php

// Overview;
// Define the various Task Groups available to the system
// Define the various Task Types within each group
// Set titles, descriptions, statuses and priorities for each group
// Set titles, descriptions and additional form fields for each task type
// Set flag to allow/disallow closed task to be reopened for each Task Group Type
// This allows <module>.tasks.php file to be created under each module,
// integrating Tasks with Cmfive modules and leveraging the existing functionality of modules
// Such files are loaded by TaskService->_loadTaskFiles()
////////////////////////////////////////////////////
////        TaskGroupType                       ////
////////////////////////////////////////////////////

/**
 * A Todo task group.
 * all properties are defined in the config.php
 *
 * @author careck
 *
 */
class TaskGroupType_TaskTodo extends TaskGroupType
{
}

////////////////////////////////////////////////
////           TaskType                     ////
////////////////////////////////////////////////

class TaskType_Todo extends TaskType
{

    public function getTaskTypeTitle()
    {
        return "Todo Item";
    }

    public function getTaskTypeDescription()
    {
        return "Use this to assign any task.";
    }
}

/**
 * A Software Development task group.
 * all properties are defined in the config.php
 *
 * @author careck
 *
 */
class TaskGroupType_SoftwareDevelopment extends TaskGroupType
{
}

/**
 *
 * Generic Programming Ticket
 *
 * Modules can be added via the Lookup table:
 * Type = "<TaskGroupTitle> Modules"
 *
 * @author admin
 *
 */
class TaskType_ProgrammingTicket extends TaskType
{

    public function getCanTaskGroupReopen()
    {
        return true;
    }

    public function getTaskTypeTitle()
    {
        return "Dev Ticket";
    }

    public function getTaskTypeDescription()
    {
        return "Use this to report any issue or feature request.";
    }

    public function getFieldFormArray(TaskGroup $taskgroup, Task $task = null)
    {
        $taskdata = null;
        if (!empty($task)) {
            $taskdata = TaskService::getInstance($this->w)->getTaskData($task->id);
        }
        return [
            [$this->getTaskTypeTitle(), "section"],
            ["Ticket Type", "select", "b_or_f", $this->getTaskDataValueForKey($taskdata, "b_or_f"), ["Issue", "Feature", "Task"], null, "form-select"],
            ["Identifier", "hidden", "ident", $this->getTaskDataValueForKey($taskdata, "ident")],
        ];
    }

    private function getTaskDataValueForKey($taskdata, $key)
    {
        if (empty($taskdata) || empty($key)) {
            return null;
        }

        if (!is_array($taskdata)) {
            return null;
        }

        foreach ($taskdata as $data) {
            if ($data->data_key == $key) {
                return $data->value;
            }
        }
    }

    public function on_before_insert(Task $task)
    {
        // Get REQUEST object instead
        if (!empty($_REQUEST["b_or_f"]) && ($_REQUEST["b_or_f"] == 'Issue' || $_REQUEST["b_or_f"] == 'Task')) {
            $task->status = "Todo";
        }
    }
}

/**
 * Cmfive support taskgroup
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class TaskGroupType_CmfiveSupport extends TaskGroupType
{
}

/**
 * Cmfive ticket class
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class TaskType_CmfiveTicket extends TaskType
{

    public function getFieldFormArray(\TaskGroup $taskgroup, \Task $task = null)
    {
        return [];
    }

    public function displayExtraButtons(\Task $task)
    {
    }

    public function displayExtraDetails(\Task $task)
    {
        return [];
    }
}
