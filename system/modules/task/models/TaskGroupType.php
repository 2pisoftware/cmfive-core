<?php

/**
 * Defines the different types of tasks which
 * can be assigned in a Task group.
 *
 * @author carsten
 *
 */
abstract class TaskGroupType
{
    public $w;

    public function __construct(Web $w)
    {
        $this->w = $w;
    }

    /**
     * Returns the title for the type
     * - override in subclass
     * - or specify in config.php using key 'task.<subclassname>.can-task-reopen'
     */
    public function getTaskGroupTypeTitle()
    {
        $value = Config::get("task." . get_class($this) . ".title");
        return !empty($value) ? $value : false;
    }

    /**
     * Specifies if a closed task can be reopened
     * - override in subclass
     * - or specify in config.php using key 'task.<subclassname>.can-task-reopen'
     *
     * @return boolean
     */
    public function getCanTaskGroupReopen()
    {
        $value = Config::get("task." . get_class($this) . ".can-task-reopen");
        return !empty($value) ? $value : false;
    }

    /**
     * Return the description for this type
     * - override in subclass
     * - or specify in config.php with key 'task.<subclassname>.description'
     */
    public function getTaskGroupTypeDescription()
    {
        $value = Config::get("task." . get_class($this) . ".description");
        return !empty($value) ? $value : false;
    }

    /**
     * Return array of php class names of concrete
     * implementations of abstract TaskType
     * - override in subclass
     * - or specify in config.php with key 'task.<subclassname>.tasktypes'
     */
    public function getTaskTypeArray()
    {
        $value = Config::get("task." . get_class($this) . ".tasktypes");
        return !empty($value) ? $value : false;
    }

    /**
     * Return array containing all
     * available statuses for tasks in
     * this group
     * - override in subclass
     * - or specify in config.php with key 'task.<subclassname>.statuses'
     */
    public function getStatusArray()
    {
        $value = Config::get("task." . get_class($this) . ".statuses");
        return !empty($value) ? $value : false;
    }

    /**
     * Return array of all available
     * priorities in this group
     * - override in subclass
     * - or specify in config.php with key 'task.<subclassname>.priorities'
     */
    public function getTaskPriorityArray()
    {
        $value = Config::get("task." . get_class($this) . ".priorities");
        return !empty($value) ? $value : false;
    }

    /**
     * Returns whether or not the given tasks priority is urgent
     *
     * @param String priority
     * @return boolean
     */
    public function isUrgentPriority($priority)
    {
        $urgent_priorities = Config::get("task." . get_class($this) . ".urgent-priorities");
        if (is_array($urgent_priorities) && count($urgent_priorities) > 0 && !empty($priority)) {
            return in_array($priority, $urgent_priorities);
        }
        return false;
    }

    /**
     * Return array of task permissions
     *
     */
    public function getPermissionsArray()
    {
    }

    /**
     * By default returns the very first status of the
     * status array if defined. Otherwise "".
     * - override in subclass
     * - or specify in config.php with key 'task.<subclassname>.default-status'
     */
    public function getDefaultStatus()
    {
        $value = Config::get("task." . get_class($this) . ".default-status");
        if (!empty($value)) {
            return $value;
        } else {
            $statusarray = $this->getStatusArray();
            if (!empty($statusarray) && sizeof($statusarray) > 0) {
                return $statusarray[0][0];
            } else {
                return "";
            }
        }
    }
    /**
     * Executed before a task is inserted into DB
     *
     * @param Task $task
     */
    public function on_before_insert(Task $task)
    {
        if (!empty($task)) {
            $task->w->callHook("task", get_class($this) . "_on_before_insert", $task);
        }
    }
    /**
     * Executed after a task has been inserted into DB
     *
     * @param Task $task
     */
    public function on_after_insert(Task $task)
    {
        if (!empty($task)) {
            $task->w->callHook("task", get_class($this) . "_on_after_insert", $task);
        }
    }
    /**
     * Executed before a task is updated in the DB
     *
     * @param Task $task
     */
    public function on_before_update(Task $task)
    {
        if (!empty($task)) {
            $task->w->callHook("task", get_class($this) . "_on_before_update", $task);
        }
    }
    /**
     * Executed after a task has been updated in the DB
     *
     * @param Task $task
     */
    public function on_after_update(Task $task)
    {
        if (!empty($task)) {
            $task->w->callHook("task", get_class($this) . "_on_after_update", $task);
        }
    }
    /**
     * Executed before a task is deleted from the DB
     *
     * @param Task $task
     */
    public function on_before_delete(Task $task)
    {
        if (!empty($task)) {
            $task->w->callHook("task", get_class($this) . "_on_before_delete", $task);
        }
    }
    /**
     * Executed after a task has been deleted from the DB
     *
     * @param Task $task
     */
    public function on_after_delete(Task $task)
    {
        if (!empty($task)) {
            $task->w->callHook("task", get_class($this) . "_on_after_delete", $task);
        }
    }
}
