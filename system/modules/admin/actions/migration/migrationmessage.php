<?php
function migrationmessage_GET(Web $w) {

$w->setLayout("layout-pretext");

$migration_module = $w->request('module');
$w->ctx("migration_module", $migration_module);

$migration_filename = $w->request('filename');
$w->ctx("migration_filename", $migration_filename);

$migration_path = $w->request('path');

if (file_exists(ROOT_PATH . '/' . $migration_path)) {
    include_once ROOT_PATH . '/' . $migration_path;

    $migration_class = explode('-', $migration_filename)[1];
    $w->ctx("migration_class", $migration_class);
    if (class_exists($migration_class)) {
        $migration = (new $migration_class(1))->setWeb($w);
        $migration_preText = $migration->preText;
        $w->ctx("migration_preText", $migration_preText);
    } else {
        $w->error("Migration Class not found for message", "/admin-migrations#batch");
    }
}
}