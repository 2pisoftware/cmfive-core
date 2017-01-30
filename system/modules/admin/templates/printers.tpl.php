<?php 
    echo Html::box("/admin/editprinter", __("Add a printer"), true);
    echo Html::table($table_data, null, "tablesorter", $table_header); 
?>
