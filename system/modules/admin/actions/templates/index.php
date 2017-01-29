<?php
function index_GET($w){
	$w->Admin->navigation($w,"Templates");
        $templates = $w->Template->findTemplates();
        $table_header = array("Title", "Module", "Category", array("Active?", true), array("Created", true), array("Modified", true), "Actions");
        $table_data = array();
        if (!empty($templates)) {
            foreach($templates as $t) {
                $table_data[]  =array(
                    $t->title, $t->module, $t->category, 
                    array($t->is_active ? "Active" : "Inactive", true),
                    array(Date("H:i d-m-Y", $t->dt_created), true), 
                    array(Date("H:i d-m-Y", $t->dt_modified), true),
                    Html::b("/admin-templates/edit/".$t->id,"Edit",false)
                );
            }
        }
        
	$w->ctx("templates_table", Html::table($table_data, null, "tablesorter", $table_header));
}