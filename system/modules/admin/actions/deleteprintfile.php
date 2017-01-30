<?php

function deleteprintfile_GET(Web $w) {
    $filename = strip_tags($_GET["filename"]);
    if (file_exists($filename)) {
        unlink($filename);
        $w->Log->info("File {$filename} deleted");
        $w->msg(__("File deleted"), "/admin/printqueue");
    }
    $w->error(__("Missing filename"), "/admin/printqueue");
}
