<?php

function printers_GET(Web $w) {
    $w->setLayout('layout-bootstrap-5');

    $printers = PrinterService::getInstance($w)->getPrinters();
    $table_data = array();
    $table_header = array("Name", "Server", "Port", "Actions");
    if (!empty($printers)) {
        foreach($printers as $printer) {
            $table_data[] = array(
                $printer->name, $printer->server, $printer->port,
                HtmlBootstrap5::box("/admin/editprinter/{$printer->id}", "Edit", true, false, null, null, 'isbox', null, 'btn btn-sm btn-primary') .
                HtmlBootstrap5::b("/admin/deleteprinter/{$printer->id}", "Delete", "Are you sure you want to delete this printer?", "deletebutton", false, "btn-sm btn-danger")
            );
        }
    }
    
    $w->ctx("table_header", $table_header);
    $w->ctx("table_data", $table_data);
}