<?php

use Html\Cmfive\QuillEditor;
use Html\Form\InputField\Checkbox;
use Html\Form\InputField\Text;
use Html\Form\Select;
use Html\Form\Textarea;

function viewtaskgrouptypes_ALL(Web $w)
{
    TaskService::getInstance($w)->navigation($w, "Manage Task Groups");

    History::add("Manage Task Groups");
    $task_groups = TaskService::getInstance($w)->getTaskGroups();
    if ($task_groups) {
        usort($task_groups, ["TaskService", "sortbyGroup"]);
    }
    // prepare column headings for display
    $headers = ["Title", "Type", "Description", "Default Assignee"];

    $line = [];

    // if task group exists, display title, group type, description, default assignee and button for specific task group info
    if ($task_groups) {
        foreach ($task_groups as $group) {
            $row = [
                Html::a(WEBROOT . "/task-group/viewmembergroup/" . $group->id, $group->title),
                $group->getTypeTitle(),
                $group->description,
                $group->getDefaultAssigneeName(),
            ];

            $line[] = $row;
        }
    } else {
        // if no groups for this group type, say as much
        $line[] = ["There are no Task Groups Configured. Please create a New Task Group.", "", "", "", ""];
    }

    // display list of task groups in the target task group type
    $w->ctx("dashboard", HtmlBootstrap5::table($line, null, "tablesorter", $headers));

    // tab: new task group
    // get generic task group permissions
    $arrassign = TaskService::getInstance($w)->getTaskGroupPermissions();
    // unset 'ALL' given all can never assign a task
    unset($arrassign[0]);

    $grouptypes = TaskService::getInstance($w)->getAllTaskGroupTypes();
    $assignees = AuthService::getInstance($w)->getUsers();
    array_unshift($assignees, ["Unassigned", "unassigned"]);

    $taskgroup_perms = TaskService::getInstance($w)->getTaskGroupPermissions();
    
    // build form to create a new task group within the target group type
    $f = Htmlbootstrap5::multiColForm([
        "Task Group Attributes" => [
            [new Text([
                "id|name" => "title",
                "label" => "Title",
            ])],
            [new QuillEditor([
                "id|name" => "description",
                "label" => "Description",
            ])],
            [new Select([
                "id|name" => "task_group_type",
                "label" => "Task Group Type",
                "options" => $grouptypes,
            ])],
            [
                new Select([
                    "id|name" => "can_assign",
                    "label" => "Who Can Assign",
                    "options" => $arrassign,
                ]),
                new Select([
                    "id|name" => "can_view",
                    "label" => "Who Can View",
                    "options" => $taskgroup_perms,
                ]),
                new Select([
                    "id|name" => "can_create",
                    "label" => "Who Can Create",
                    "options" => $taskgroup_perms,
                ]),
            ],
            [
                new Select([
                    "id|name" => "default_task_type",
                    "label" => "Default Task Type",
                ]),
                new Select([
                    "id|name" => "default_priority",
                    "label" => "Default Priority",
                ])
            ],
            [
                new Select([
                    "id|name" => "default_assignee_id",
                    "label" => "Default Assignee",
                    "options" => $assignees,
                ]),
                new Checkbox([
                    "id|name" => "is_automatic_subscription",
                    "label" => "Automatic Subscription",
                    "value" => TaskGroup::$_DEFAULT_AUTOMATIC_SUBSCRIPTION,
                ]),
            ]
        ]
    ], $w->localUrl("/task-group/createtaskgroup"), "POST");

    // display form
    $w->ctx("creategroup", $f);
}
