<?php 
    echo HtmlBootstrap5::box("/admin/editprinter", "Add a printer", true, false, null, null, 'isbox', null, 'btn btn-sm btn-primary');
    echo HtmlBootstrap5::table($table_data, null, "tablesorter", $table_header); 
