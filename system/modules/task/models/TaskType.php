<?php

/**
 * Abstract class describing types of
 * Tasks
 *
 */
abstract class TaskType
{
    public $w;

    public function __construct(Web $w)
    {
        $this->w = $w;
    }

    public function getTaskTypeTitle()
    {
        return Config::get("task." . get_class($this) . ".title");
    }

    public function getTaskTypeDescription()
    {
        return Config::get("task." . get_class($this) . ".description");
    }

    /**
     * return a value that should be added to the search index for this task
     */
    public function addToIndex(Task $task)
    {
    }

    /**
     * return an array similar to the Html::form
     * which describes the fields available for this
     * task type and the way they should be presented in
     * task details.
     *
     */
    public function getFieldFormArray(TaskGroup $taskgroup, Task $task = null)
    {
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
    
    /**
     * Return an array of options for time types
     * - override in subclass
     * - or specify in config.php using key 'task.<subclassname>.time-types'
     *
     * @return array
     */
    public function getTimeTypes()
    {
        return Config::get("task." . get_class($this) . ".time-types");
    }
}
