<?php

function printfile_GET(Web $w) {
    if (empty($_GET["filename"])) {
        $w->out("No filename specified");
    }
    $form = array(
        "Print to" => array(
            array(array("Printer", "select", "printer_id", null, $w->Printer->getPrinters(), "null")),
            array(array("Filename", "hidden", "file", $_GET["filename"])))
    );
    
    $w->out(Html::multiColForm($form, "/admin/printfile", "POST", "Print", null, null, null, "_self", true, array("printer_id")));
}

function printfile_POST(Web $w) {
    $printer = $w->Printer->getPrinter($_POST["printer_id"]);
    if (empty($printer->id)) {
        $w->out("Printer does not exist");
    }
    
    $w->Printer->printJob(urldecode($_POST["file"]), $printer);
    $w->msg("File has been sent to the printer", "/admin/printqueue");
}
