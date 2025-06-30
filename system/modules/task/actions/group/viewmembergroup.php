<?php

// clicking the 'More Info' button for a task group gives all details specific to this group
// including group attributes and group membership
function viewmembergroup_GET(Web $w) {
    $p = $w->pathMatch("id");

    // tab: Members
    // get all members in a task group given a task group ID
    $member_group = TaskService::getInstance($w)->getMemberGroup($p['id']);

    // get the group attributes given a task group ID
    $taskgroup = TaskService::getInstance($w)->getTaskGroup($p['id']);
    if (empty($taskgroup->id)) {
        $w->error("Taskgroup not found", '/task');
    }

    // put the group title into the page heading
    TaskService::getInstance($w)->navigation($w, "Task Group - " . StringSanitiser::sanitise($taskgroup->title));

    History::add("Task Group: " . StringSanitiser::sanitise($taskgroup->title), null, $taskgroup);

    // set columns headings for display of members
    $line[] = ["Member", "Role", ""];

    // if their are members, display their full name, role and buttons to edit or delete the member
    if ($member_group) {
        foreach ($member_group as $member) {
            $line[] = [TaskService::getInstance($w)->getUserById($member->user_id), $member->role,
                HtmlBootstrap5::box(WEBROOT . "/task-group/viewmember/" . $member->id, " Edit ", true, null, null, null, null, null, "bg-primary text-light") .
                "&nbsp;&nbsp;" .
                HtmlBootstrap5::box(WEBROOT . "/task-group/deletegroupmember/" . $member->id, " Delete ", true,  null, null, null, null, null, "bg-danger text-light")
            ];
        }
    } else {
        // if there are no members, say as much
        $line[] = ["Group currently has no members. Please Add New Members.", "", ""];
    }

    // display list of group members
    $w->ctx("viewmembers", HtmlBootstrap5::table($line, null, "tablesorter", true));

    $w->ctx("taskgroup", $taskgroup);
}
