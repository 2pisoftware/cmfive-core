<?php

function printers_GET(Web $w) {
    $printers = $w->Printer->getPrinters();
    $table_data = array();
    $table_header = array(__("Name"), __("Server"), __("Port"), __("Actions"));
    if (!empty($printers)) {
        foreach($printers as $printer) {
            $table_data[] = array(
                $printer->name, $printer->server, $printer->port,
                Html::box("/admin/editprinter/{$printer->id}", __("Edit"), true) .
                Html::b("/admin/deleteprinter/{$printer->id}", __("Delete"), __("Are you sure you want to delete this printer?"))
            );
        }
    }
    
    $w->ctx("table_header", $table_header);
    $w->ctx("table_data", $table_data);
}
