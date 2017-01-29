<?php

function editprinter_GET(Web $w) {
    $p = $w->pathMatch("id");
    $printer = new Printer($w);
    if (!empty($p["id"])){
        $printer = $w->Printer->getPrinter($p["id"]);
    }
    
    $form = array(
        "Details" => array(
            array(array("Printer name", "text", "name", $printer->name)),
            array(array("Server", "text", "server", $printer->server)),
            array(array("Port", "text", "port", $printer->port))
        )
    );
            
    $w->out(Html::multiColForm($form, "/admin/editprinter/{$p['id']}"));
}

function editprinter_POST(Web $w) {
    $p = $w->pathMatch("id");
    $printer = new Printer($w);
    if (!empty($p["id"])){
        $printer = $w->Printer->getPrinter($p["id"]);
    }
    
    $printer->fill($_POST);
    $printer->insertOrUpdate();
    
    $w->msg("Printer added", "/admin/printers");
}