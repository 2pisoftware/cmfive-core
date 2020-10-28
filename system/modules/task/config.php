<?php

Config::set('task', array(
    'version' => '0.8.0',
    'active' => true,
    'path' => 'system/modules',
    'topmenu' => true,
    "dependencies" => array(
        "floriansemm/official-library-php-email-parser" => "~1.0"
    ),
    'search' => array('Tasks' => "Task"),
    'hooks' => array(
        'core_web',
        'core_dbobject',
        'comment',
        'attachment',
		'timelog',
		'admin',
		'task'
    ),
    'ical' => array(
        'send' => false
    ),
    'timelog' => array(
        'Task'
    ),
    'processors' => array(
        'TicketEmailProcessor'
    )
));

// Set form mapping objects
Config::append('form.mapping', [
	'Task', 'TaskGroup'
]);

//========= Properties of Task Type Todo ==================

Config::append('task.TaskType_Todo',array(
	'time-type' => array("Ordinary Hours", "Overtime", "Weekend"),
));

//========= Properties of Taskgroup Type Todo ============

Config::append('task.TaskGroupType_TaskTodo', array(
	'title' => 'To Do',
	'description' => 'This is a TODO list. Use this for assigning any work.',
	'can-task-reopen' => true,
	'tasktypes' => array("Todo" => "To Do"),
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

Config::append('task.TaskType_ProgrammingTicket',array(
	'time-type' => array("Ordinary Hours", "Overtime", "Weekend"),
));

//========= Properties of Taskgroup Type SoftwareDevelopment ==

Config::append('task.TaskGroupType_SoftwareDevelopment', array(
	'title' => 'Software Development',
	'description' => 'Use this for tracking software development tasks.',
	'can-task-reopen' => true,
	'tasktypes' => array(
	    "ProgrammingTicket" => "Programming Task"),
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

Config::set('task.TaskGroupType_CmfiveSupport', [
	'title' => 'Cmfive Support',
	'description' => 'Tracking Support Requests.',
	'can-task-reopen' => true,
	'tasktypes' => ["CmfiveTicket" => "Support Ticket"],
	'statuses' => [
			["New", false],
            ["Assigned", false],
            ["WIP", false],
            ["Wait for Comment", false],
            ["Done", true], // is closing
            ["Rejected", true]], // is closing
	'priorities' => ["Critical","Major", "Minor", "Normal"],
	'urgent-priorities' => ["Critical", "Major"]
]);

Config::set('task.TaskType_CmfiveTicket', [
	'title' => "Support Ticket",
	'description' => "A Support Ticket.",
	'time-types' => ["Business Hours", "After Hours", "Quoted", "Non-Billable", "Internal"]
]);
