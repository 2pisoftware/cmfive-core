<?php

function index_GET(Web $w)
{
    $w->ctx('title', 'Cmfive Maintenance');

    // Get server name
    $w->ctx('server', php_uname('s m'));
    $load = null;

    // Get load avg if on linux
    if (stristr(PHP_OS, "Linux")) {
        $load = sys_getloadavg();
    }
    $w->ctx('load', $load);

    // Get total number of indexed objects
    $w->ctx('count_indexed', $w->db->get('object_index')->count());

    $size = $w->db->sql('SELECT table_schema as db, Round(Sum(data_length + index_length) / 1024 / 1024, 1) as size FROM information_schema.tables WHERE table_schema = "' . Config::get('database.database') . '"')->fetchElement('size');
    $w->ctx('db_size', $size);

    $w->ctx('audit_row_count', $w->db->get('audit')->count());

    if (Config::get('file.adapters.local.active') !== true) {
        $w->ctx('cache_image_count', FileService::getInstance($w)->countFilesInDirectory(WEBROOT . '/cache'));
    }

    $w->ctx("number_of_printers", 0);
}
