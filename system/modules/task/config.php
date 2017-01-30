<?php

Config::set('task', array(
    'version' => '0.8.0',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    'search' => array('Tasks' => "Task"),
    'hooks' => array(
        'core_web',
        'core_dbobject',
        'comment',
        'attachment',
		'timelog',
		'admin'
    ),
    'ical' => array(
        'send' => false
    ),
    'timelog' => array(
        'Task'
    )
));

// Set form mapping objects
Config::append('form.mapping', [
	'Task', 'TaskGroup'
]);

//========= Properties of Task Type Todo ==================

Config::set('task.TaskType_Todo',array(
	'time-type' => array(__("Ordinary Hours"), __("Overtime"), __("Weekend")),
));

//========= Properties of Taskgroup Type Todo ============

Config::set('task.TaskGroupType_TaskTodo', array(
	'title' => 'To Do',
	'description' => 'This is a TODO list. Use this for assigning any work.',
	'can-task-reopen' => true,
	'tasktypes' => array("Todo" => __("To Do")),
	'statuses' => array(
			array("New", false),
            array("Assigned", false),
            array("Wip", false),
            array("Pending", false),
            array("Done", true), // is closing
            array("Rejected", true)), // is closing
	'priorities' => array("Urgent", "Normal", "Nice to have"),
	'urgent-priorities' => array("Urgent")
));

//========= Properties of Task Type Programming Task =================

Config::set('task.TaskType_ProgrammingTicket',array(
	'time-type' => array(__("Ordinary Hours"), __("Overtime"), __("Weekend")),
));

//========= Properties of Taskgroup Type SoftwareDevelopment ==

Config::set('task.TaskGroupType_SoftwareDevelopment', array(
	'title' => 'Software Development',
	'description' => 'Use this for tracking software development tasks.',
	'can-task-reopen' => true,
	'tasktypes' => array(
	    "ProgrammingTicket" => __("Programming Task")),
	'statuses' => array(
		array("Idea", false),
		array("On Hold", false),
		array("Backlog", false),
		array("Todo", false),
		array("WIP", false),
		array("Testing", false),
		array("Review", false),
		array("Deploy", false),
		array("Live", true), // is closing
		array("Rejected", true)), // is closing
	'priorities' => array("Urgent", "Normal", "Nice to have"),
	'urgent-priorities' => array("Urgent")
));
