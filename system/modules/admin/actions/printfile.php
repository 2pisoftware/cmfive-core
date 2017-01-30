<?php

function printfile_GET(Web $w) {
    if (empty($_GET["filename"])) {
        $w->out(__("No filename specified"));
    }
    $form = array(
        "Print to" => array(
            array(array(__("Printer"), "select", "printer_id", null, $w->Printer->getPrinters(), "null")),
            array(array(__("Filename"), "hidden", "file", $_GET["filename"])))
    );
    
    $w->out(Html::multiColForm($form, "/admin/printfile", "POST", __("Print"), null, null, null, "_self", true, array("printer_id")));
}

function printfile_POST(Web $w) {
    $printer = $w->Printer->getPrinter($_POST["printer_id"]);
    if (empty($printer->id)) {
        $w->out(__("Printer does not exist"));
    }
    
    $w->Printer->printJob(urldecode($_POST["file"]), $printer);
    $w->msg(__("File has been sent to the printer"), "/admin/printqueue");
}
