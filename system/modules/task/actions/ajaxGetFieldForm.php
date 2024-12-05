<?php

function ajaxGetFieldForm_ALL(Web $w)
{
    $p = $w->pathMatch("task_type", "task_group_id", "task_id");

    if (empty($p['task_group_id']) || empty($p['task_type'])) {
        return;
    }

    $task_type = TaskService::getInstance($w)->getTaskTypeObject($p['task_type']);
    if (empty($task_type)) {
        return;
    }

    $task_group = TaskService::getInstance($w)->getTaskgroup($p['task_group_id']);
    if (empty($task_group->id)) {
        return;
    }

    if (empty($p["task_id"])) {
        return;
    }

    $task = TaskService::getInstance($w)->getTask($p['task_id']);
    if (empty($task)) {
        return;
    }

    $task_type_form = $task_type->getFieldFormArray($task_group, $task);

    // Display historic data (if the taskgroup type has changed, for example)
    $display_keys = [];
    if (!empty($task_type_form)) {
        foreach ($task_type_form as $task_type_row) {
            if (isset($task_type_row[2])) {
                $display_keys[] = $task_type_row[2];
            }
        }
    }

    // Add conditions to query
    $old_task_data_query = $w->db->get("task_data");

    if ($task !== null) {
        $old_task_data_query = $old_task_data_query->where("task_id", $task->id);
    }

    array_map(function ($data_key) use ($old_task_data_query) {
        $old_task_data_query->where("data_key != ?", $data_key);
    }, $display_keys ?: []);

    // Map to format for Html::table
    $table_data = [];
    array_map(function ($data_row) use (&$table_data) {
        $table_data[] = [$data_row['data_key'], $data_row['value']];
    }, $old_task_data_query->fetchAll() ?: []);

    // Output

    $w->setLayout(null);
    $w->out(json_encode([
        "historial" => HtmlBootstrap5::table($table_data, null, "small-12", ["Data key", "Value"]),
        "current" => HtmlBootstrap5::form($task_type_form, "/task/edit", null, null, "form_fields_form"),
    ]));

    // $w->out(json_encode(
    //     (count($table_data) > 0 ? "<table style='width: 100%; margin-bottom: 0px;'><tr><td class='section'>Historical task data</td></tr></table>" . Html::table($table_data, null, "small-12", ["Data key", "Value"]) : '') .
    //         (!empty($task_type_form) ? Html::form($task_type_form, "/task/edit", null, null, "form_fields_form") : '')
    // ));
}
