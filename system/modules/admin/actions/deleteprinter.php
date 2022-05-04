<?php

function deleteprinter_ALL(Web $w) {
    $p = $w->pathMatch("id");

    if (!empty($p["id"])){
        $printer = PrinterService::getInstance($w)->getPrinter($p["id"]);
        if (!empty($printer->id)) {
            $printer->delete();
            $w->msg("Printer deleted", "/admin");
        }
    }
    
    $w->error("Could not find printer", "/admin");

}

