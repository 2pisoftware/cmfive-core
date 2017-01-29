<?php

function deleteprintfile_GET(Web $w) {
    $filename = strip_tags($_GET["filename"]);
    if (file_exists($filename)) {
        unlink($filename);
        $w->Log->info("File {$filename} deleted");
        $w->msg("File deleted", "/admin/printqueue");
    }
    $w->error("Missing filename", "/admin/printqueue");
}