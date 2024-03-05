<?php

function editprinter_GET(Web $w)
{
    $p = $w->pathMatch("id");
    $printer = new Printer($w);
    if (!empty($p["id"])) {
        $printer = PrinterService::getInstance($w)->getPrinter($p["id"]);
    }

    $form = [
        "Details" => [
            [["Printer name", "text", "name", $printer->name]],
            [["Server", "text", "server", $printer->server]],
            [["Port", "text", "port", $printer->port]]
        ]
    ];

    $w->out(HtmlBootstrap5::multiColForm($form, "/admin/editprinter/{$p['id']}", "POST", "Save", null, null, null, "_self", true, Printer::$_validation));
}

function editprinter_POST(Web $w)
{
    $p = $w->pathMatch("id");
    $printer = new Printer($w);
    if (!empty($p["id"])) {
        $printer = PrinterService::getInstance($w)->getPrinter($p["id"]);
    }

    $printer->fill($_POST);
    $printer->insertOrUpdate();

    $w->msg("Printer added", "/admin/printers");
}
