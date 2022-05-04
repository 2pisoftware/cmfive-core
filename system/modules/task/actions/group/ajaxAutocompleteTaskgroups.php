<?php

function ajaxAutocompleteTaskgroups_GET(Web $w)
{
    $w->setLayout(null);
    $term = Request::string("term");

    $taskgroups = TaskService::getInstance($w)->getObjects("TaskGroup", ["title LIKE ?" => "%{$term}%", "is_deleted" => 0, "is_active" => 1], false, 'title ASC');
    $return_data = [];
    if (!empty($taskgroups)) {
        $taskgroups = array_filter($taskgroups, function ($taskgroup) use ($w) {
            return $taskgroup->canView(AuthService::getInstance($w)->user());
        });

        if (!empty($taskgroups)) {
            foreach ($taskgroups as $taskgroup) {
                $return_data[] = ["label" => $taskgroup->getSelectOptionTitle(), "value" => $taskgroup->getSelectOptionValue()];
            }
        }
    }

    echo json_encode($return_data);
}
