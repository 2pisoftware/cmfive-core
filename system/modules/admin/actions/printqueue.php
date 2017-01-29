<?php

function printqueue_GET(Web $w) {
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
            Html::a("/uploads/print/" . $filename, $filename),
            // Function below in functions.php
            humanReadableBytes($object->getSize()),
            date("H:i d/m/Y", filectime($name)),
            Html::box("/admin/printfile?filename=" . urlencode($name), "Print", true) . " " .
            Html::b("/admin/deleteprintfile?filename=" . urlencode($name), "Delete", "Are you sure you want to remove this file? (This is irreversible)")
        );
    }

    $w->out(Html::table($table_data, null, "tablesorter", $table_header));
}