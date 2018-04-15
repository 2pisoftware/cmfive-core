<?php

function list_GET(Web $w) {
    $w->setLayout('layout-f6');

    // Task list action
    History::add('Task List');

    $w->ctx("assignees", json_encode(array_map(function($user) {return ['value' => $user['fullname'], 'text' => $user['fullname']];}, $w->db->query("select distinct t.assignee_id, concat(c.firstname, ' ', c.lastname) as fullname from task t inner join `user` u on u.id = t.assignee_id inner join contact c on u.contact_id = c.id;")->fetchAll())));
    $w->ctx("creators", json_encode(array_map(function($user) {return ['value' => $user->id, 'text' => $user->getFullName()];}, $w->Auth->getUsers())));
    $w->ctx("task_groups", json_encode(array_map(function($task_group) {return ['value' => $task_group['title'], 'text' => $task_group['title']];}, $w->db->query("select distinct t.task_group_id, tg.title from task t inner join task_group tg on t.task_group_id = tg.id;")->fetchAll())));
    $w->ctx("task_types", json_encode(array_map(function($task_type) {return ['value' => $task_type['task_type'], 'text' => $task_type['task_type']];}, $w->db->get("task")->select()->select("DISTINCT task_type")->fetchAll())));
    $w->ctx("priority_list", json_encode(array_map(function($priority) {return ['value' => $priority['priority'], 'text' => $priority['priority']];}, $w->db->query("select distinct priority from task where priority != '' or priority != null")->fetchAll())));
    $w->ctx("task_statuslist", json_encode(array_map(function($task_status) {return ['value' => $task_status['status'], 'text' => $task_status['status']];}, $w->db->get("task")->select()->select("DISTINCT status")->fetchAll())));

    $w->ctx("priority_list_select", json_encode(array_map(function($priority) {return ['id' => $priority['priority'], 'text' => $priority['priority']];}, $w->db->query("select distinct priority from task where priority != '' or priority != null")->fetchAll())));
    $w->ctx("task_statuslist_select", json_encode(array_map(function($task_status) {return ['id' => $task_status['status'], 'text' => $task_status['status']];}, $w->db->get("task")->select()->select("DISTINCT status")->fetchAll())));
}