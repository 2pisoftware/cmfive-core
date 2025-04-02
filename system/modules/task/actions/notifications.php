<?php

function notifications_GET(Web $w) {
	$line = [["Task Group", "Your Role", "Creator", "Assignee", "All Others", ""]];
    $user_taskgroup_members = TaskService::getInstance($w)->getMemberGroups(AuthService::getInstance($w)->user()->id);
    if ($user_taskgroup_members) {
        usort($user_taskgroup_members, array("TaskService", "sortbyRole"));

        foreach ($user_taskgroup_members as $member) {
            $taskgroup = $member->getTaskGroup();
            $value_array = [];
            $notify = TaskService::getInstance($w)->getTaskGroupUserNotify(AuthService::getInstance($w)->user()->id, $member->task_group_id);
            if ($notify) {
                foreach ($notify as $n) {
                    $value = ($n->value == "0") ? "No" : "Yes";
                    $value_array[$n->role][$n->type] = $value;
                }
            } else {
                $notify = TaskService::getInstance($w)->getTaskGroupNotify($member->task_group_id);
                if ($notify) {
                    foreach ($notify as $n) {
                        $value = ($n->value == "0") ? "No" : "Yes";
                        $value_array[$n->role][$n->type] = $value;
                    }
                }
            }

            if ($taskgroup->getCanIView()) {
                $title = TaskService::getInstance($w)->getTaskGroupTitleById($member->task_group_id);
                $role = strtolower($member->role);

                $line[] = array(
                    $title,
                    ucfirst($role),
                    @$value_array[$role]["creator"],
                    @$value_array[$role]["assignee"],
                    @$value_array[$role]["other"],
                    HtmlBootstrap5::box(href: $w->localUrl("/task/updateusergroupnotify/" . $member->task_group_id), title: " Edit ", button: true, class: 'btn btn-primary')
                );
            }
            unset($value_array);
        }

        // display list
        $w->ctx("notify", HtmlBootstrap5::table($line, null, "tablesorter", true));
    }
}

function notifications_POST(Web $w) {
	
}
