<?php

function printers_GET(Web $w) {
    $w->setLayout('layout-bootstrap-5');
    $w->ctx('title', 'Printer List');

    $printers = PrinterService::getInstance($w)->getPrinters();
    $table_data = [];
    $table_header = ["Name", "Server", "Port", "Actions"];
    if (!empty($printers)) {
        foreach($printers as $printer) {
            $table_data[] = [
                $w->safePrint($printer->name), $w->safePrint($printer->server), $w->safePrint($printer->port),
                HtmlBootstrap5::box("/admin/editprinter/{$printer->id}", "Edit", true, false, null, null, 'isbox', null, 'btn btn-sm btn-primary') .
                HtmlBootstrap5::b("/admin/deleteprinter/{$printer->id}", "Delete", "Are you sure you want to delete this printer?", "deletebutton", false, "btn-sm btn-danger")
            ];
        }
    }
    
    $w->ctx("table_header", $table_header);
    $w->ctx("table_data", $table_data);
}