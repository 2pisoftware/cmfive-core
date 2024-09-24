<?php
// show task activity for the group and date span specified

use Html\Form\InputField\Date;
use Html\Form\Select;

function taskweek_ALL(Web &$w)
{
    TaskService::getInstance($w)->navigation($w, "");

    // if no group then no group
    $taskgroup = Request::mixed('taskgroup');
    // if no group member then no group member
    $assignee = Request::mixed('assignee');
    // if no from date then 7 days ago
    $from = Request::string('dt_from', TaskService::getInstance($w)->getLastWeek());
    // if no to date then today
    $to = Request::string('dt_to', date("d/m/Y"));
    // display
    $w->ctx("from", $from);
    $w->ctx("to", $to);

    // get all tasks in my groups answering criteria
    $tasks = TaskService::getInstance($w)->getTaskWeek($taskgroup, $assignee, $from, $to);

    // set task activity heading
    $line = [
        ["An overview of the activity in Tasks: " . $from . " to " . $to],
    ];
    if ($tasks)
    {
        // dont wanna keep displaying same date so set a variable for comparison
        $olddate = "";
        $i = 0;
        foreach ($tasks as $task)
        {
            $taskgroup = TaskService::getInstance($w)->getTaskGroup($task['task_group_id']);
            $caniview = $taskgroup->getCanIView();

            if ($caniview)
            {
                // if current task date = previous task date, dont display
                if (formatDate($task['dt_modified']) != $olddate)
                {
                    // if this is not the first record, display emtpy row between date lists
                    if ($i > 0)
                    {
                        $line[] = ["&nbsp;"];
                    }
                    // display fancy date
                    $line[] = ["<b>" . date("l jS F, Y", strtotime($task['dt_modified'])) . "</b>"];
                }
                // display comments. if no group selected, display with link to task list with group preselected
                $thisgroup = ($taskgroup != "") ? "" : "<a title=\"View Task Group\" href=\"" . WEBROOT . "/task/tasklist/?taskgroups=" . $task['task_group_id'] . "\">" . TaskService::getInstance($w)->getTaskGroupTitleById($task['task_group_id']) . "</a>:&nbsp;&nbsp;";
                $line[] = ["<dd>" . date("g:i a", strtotime($task['dt_modified'])) . " - " . $thisgroup . "<a title=\"View Task Details\" href=\"" . WEBROOT . "/task/edit/" . $task['id'] . "\"><b>" . $task['title'] . "</b></a>: " . TaskService::getInstance($w)->findURL($task['comment']) . " - " . TaskService::getInstance($w)->getUserById($task['creator_id']) . "</dd>"];
                $olddate = formatDate($task['dt_modified']);
                $i++;
            }
        }
    }
    else
    {
        // if no tasks found, say as much
        $line[] = ["No Task Activity found for given selections."];
    }

    // display
    $lines = HtmlBootstrap5::table($line, null, "tablesorter", true);
    $w->ctx("taskweek", $lines);

    // get list of groups of which i am a member
    $mygroups = TaskService::getInstance($w)->getMemberGroups($_SESSION['user_id']);
    if ($mygroups)
    {
        foreach ($mygroups as $mygroup)
        {
            $taskgroup = TaskService::getInstance($w)->getTaskGroup($mygroup->task_group_id);
            $caniview = $taskgroup->getCanIView();

            if ($caniview)
            {
                $group[$mygroup->task_group_id] = [TaskService::getInstance($w)->getTaskGroupTitleById($mygroup->task_group_id), $mygroup->task_group_id];

                // for those groups of which i am a member, get list of all members for display in Assignee & Creator dropdowns
                $mymembers = TaskService::getInstance($w)->getMembersInGroup($mygroup->task_group_id);
                foreach ($mymembers as $mymem)
                {
                    $members[$mymem[1]] = [$mymem[0], $mymem[1]];
                }
            }
        }
        sort($members);
    }

    $search_form = HtmlBootstrap5::filter("Search Tasks", [
        new Select([
            "id|name" => "taskgroup",
            "label" => "Task Group",
            "options" => $group
        ]),
        new Select([
            "id|name" => "assignee",
            "label" => "User",
            "options" => $members
        ]),
        new Date([
            "id|name" => "dt_from",
            "label" => "From",
            "value" => DateTime::createFromFormat("d/m/Y", $from)->format("Y-m-d"), // annoying
        ]),
        new Date([
            "id|name" => "dt_to",
            "label" => "To date",
            "value" => DateTime::createFromFormat("d/m/Y", $to)->format("Y-m-d"), // annoying
        ]),
    ], $w->localUrl("/task/taskweek"), "POST");

    $w->ctx("search_form", $search_form);
}
