<?php

function editprinter_GET(Web $w) {
    $p = $w->pathMatch("id");
    $printer = new Printer($w);
    if (!empty($p["id"])){
        $printer = $w->Printer->getPrinter($p["id"]);
    }
    
    $form = array(
        __("Details") => array(
            array(array(__("Printer name"), "text", "name", $printer->name)),
            array(array(__("Server"), "text", "server", $printer->server)),
            array(array(__("Port"), "text", "port", $printer->port))
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
    
    $w->msg(__("Printer added"), "/admin/printers");
}
