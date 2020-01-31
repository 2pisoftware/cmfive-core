<?php
function index_GET($w)
{
    $w->Admin->navigation($w, "Templates");
    $templates = $w->Template->findTemplates(null, null, true);
    $table_header = [
        "Title", "Module", "Category",
        ["Active?", true],
        ["Created", true],
        ["Modified", true],
        "Actions",
    ];

    $table_data = [];
    if (!empty($templates)) {
        foreach ($templates as $t) {
            $table_data[]  = [
                $t->title, $t->module, $t->category,
                [$t->is_active ? "Active" : "Inactive", true],
                [Date("H:i d-m-Y", $t->dt_created), true],
                [Date("H:i d-m-Y", $t->dt_modified), true],
                Html::b("/admin-templates/edit/" . $t->id, "Edit", false),
            ];
        }
    }

    $w->ctx("templates_table", Html::table($table_data, null, "tablesorter", $table_header));
}
