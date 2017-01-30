<?php

function deleteprinter_ALL(Web $w) {
    $p = $w->pathMatch("id");

    if (!empty($p["id"])){
        $printer = $w->Printer->getPrinter($p["id"]);
        if (!empty($printer->id)) {
            $printer->delete();
            $w->msg(__("Printer deleted"), "/admin");
        }
    }
    
    $w->error(__("Could not find printer"), "/admin");

}

