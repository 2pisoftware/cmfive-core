<?php 
    echo Html::box("/admin/editprinter", "Add a printer", true);
    echo Html::table($table_data, null, "tablesorter", $table_header); 
?>