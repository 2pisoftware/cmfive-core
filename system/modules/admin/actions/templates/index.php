<?php
function index_GET($w){
	$w->Admin->navigation($w,__("Templates"));
        $templates = $w->Template->findTemplates();
        $table_header = array(__("Title"), __("Module"), __("Category"), array(__("Active?"), true), array(__("Created"), true), array(__("Modified"), true), __("Actions"));
        $table_data = array();
        if (!empty($templates)) {
            foreach($templates as $t) {
                $table_data[]  =array(
                    $t->title, $t->module, $t->category, 
                    array($t->is_active ? __("Active") : __("Inactive"), true),
                    array(Date("H:i d-m-Y", $t->dt_created), true), 
                    array(Date("H:i d-m-Y", $t->dt_modified), true),
                    Html::b("/admin-templates/edit/".$t->id,__("Edit"),false)
                );
            }
        }
        
	$w->ctx("templates_table", Html::table($table_data, null, "tablesorter", $table_header));
}
