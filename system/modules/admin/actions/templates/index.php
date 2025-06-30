<?php
function index_GET($w)
{
    $w->setLayout('layout-bootstrap-5');
    AdminService::getInstance($w)->navigation($w, "Templates");
    $templates = TemplateService::getInstance($w)->findTemplates(null, null, true);
    $table_header = [
        "Title", "Module", "Category",
        ["Active?", true],
        // ["Created", true],
        // ["Modified", true],
        "Actions",
    ];

    $table_data = [];
    if (!empty($templates)) {
        foreach ($templates as $t) {
            $table_data[]  = [
                StringSanitiser::sanitise($t->title), StringSanitiser::sanitise($t->module), StringSanitiser::sanitise($t->category),
                [$t->is_active ? "Active" : "Inactive", true],
                // [Date("H:i d-m-Y", $t->dt_created), true],
                // [Date("H:i d-m-Y", $t->dt_modified), true], 
                HtmlBootstrap5::b("/admin-templates/edit/" . $t->id, "Edit", null, null, false, "btn-sm btn-secondary"),
            ];
        }
    }

    $w->ctx("templates_table", HtmlBootstrap5::table($table_data, null, "tablesorter", $table_header));
}
