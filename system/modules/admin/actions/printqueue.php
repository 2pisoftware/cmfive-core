<?php

function printqueue_GET(Web $w) {
    $w->setLayout('layout-bootstrap-5');

    $print_folder = FILE_ROOT . "print";
    $path = realpath($print_folder);
    
    // Check if folder exists
    if ($path === false) {
        // Make print folder (If you specify a full path, use the recursion flag because it seems to crash without it in unix)
        // Other wise you would need to chdir to the parent folder, create and change back to wherever execution currently was at
        mkdir($print_folder, 0777, true);
        $path = realpath($print_folder);
    }
    $exclude = array("THUMBS.db");
    $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    
    $table_data = array();
    $table_header = array("Name", "Size", "Date Created", "Actions");
    
    foreach($objects as $name => $object){
        $filename = $object->getFilename();
        // Ignore files starting with '.' and in exclude array
        if ($filename[0] === '.' || in_array($filename, $exclude)) {
            continue;
        }
        
        $table_data[] = array(
            HtmlBootstrap5::a("/uploads/print/" . $filename, $filename),
            // Function below in functions.php
            humanReadableBytes($object->getSize()),
            date("H:i d/m/Y", filectime($name)),
            HtmlBootstrap5::box("/admin/printfile?filename=" . urlencode($name), "Print", true, false, null, null, 'isbox', null, 'btn btn-sm btn-primary') . " " .
            HtmlBootstrap5::b("/admin/deleteprintfile?filename=" . urlencode($name), "Delete", "Are you sure you want to remove this file? (This is irreversible)", "deletebutton", false, "btn-sm btn-danger")
        );
    }

    $w->out(HtmlBootstrap5::table($table_data, null, "tablesorter", $table_header));
}