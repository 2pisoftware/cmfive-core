<?php

function list_GET(Web $w) {
    $w->setLayout('layout-f6');

    // Task list action
    History::add('Task List');

    $w->ctx("assignees", $w->Task->getAssignees());
    $w->ctx("creators", $w->Task->getCreators());
    $w->ctx("task_groups", $w->Task->getTaskGroups());
    $w->ctx("task_types", $w->Task->getTaskTypesList());
    $w->ctx("priority_list", $w->Task->getPriorityList());
    $w->ctx("status_list", $w->Task->getStatusList());
}