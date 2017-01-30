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
////		TaskGroupType						////
////////////////////////////////////////////////////

/**
 * A Todo task group.
 * all properties are defined in the config.php
 * 
 * @author careck
 *
 */
class TaskGroupType_TaskTodo extends TaskGroupType {
}

////////////////////////////////////////////////
////		TaskType						////
////////////////////////////////////////////////

class TaskType_Todo extends TaskType {

    function getTaskTypeTitle() {
        return __("Todo Item");
    }

    function getTaskTypeDescription() {
        return __("Use this to assign any task.");
    }

}

/**
 * A Software Development task group.
 * all properties are defined in the config.php
 *
 * @author careck
 *
 */
class TaskGroupType_SoftwareDevelopment extends TaskGroupType {
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
class TaskType_ProgrammingTicket extends TaskType {

    function getCanTaskGroupReopen() {
        return true;
    }

    function getTaskTypeTitle() {
        return __("Dev Ticket");
    }

    function getTaskTypeDescription() {
        return __("Use this to report any issue or feature request.");
    }

    function getFieldFormArray(TaskGroup $taskgroup, Task $task = null) {
        $taskdata = null;
        if (!empty($task)) {
            $taskdata = $this->w->Task->getTaskData($task->id);
        }
        return array(
            array($this->getTaskTypeTitle(), "section"),
            array(__("Ticket Type"), "select", "b_or_f", $this->getTaskDataValueForKey($taskdata, "b_or_f"), array(__("Issue"), __("Feature"), __("Task"))),
            array(__("Identifier"), "hidden", "ident", $this->getTaskDataValueForKey($taskdata, "ident")),
        );
    }

    private function getTaskDataValueForKey($taskdata, $key) {
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

    function on_before_insert(Task $task) {
        // Get REQUEST object instead
        if (!empty($_REQUEST["b_or_f"]) && ($_REQUEST["b_or_f"] == 'Issue' || $_REQUEST["b_or_f"] == 'Task')) {
            $task->status = "Todo";
        }
    }


}
