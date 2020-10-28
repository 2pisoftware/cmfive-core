<?php

use Gaufrette\Filesystem;
use Gaufrette\Adapter\Local as LocalAdapter;

function ajax_backupdatabase_GET(Web $w)
{
    if (AuthService::getInstance($w)->user() === null || !AuthService::getInstance($w)->user()->is_admin) {
        return;
    }

    $datestamp = date("Y-m-d-H-i");
    $filedir = ROOT_PATH . "/cache/";

    $backupformat = Config::get('admin.database.output');
    $filename = "$datestamp.$backupformat";
    $command = null;

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $command = Config::get('admin.database.command.windows');
    } else {
        $command = Config::get('admin.database.command.unix');
    }

    if (!empty($command)) {
        $command = str_replace(['$username', '$password', '$dbname', '$filename'], [Config::get('database.username'), Config::get('database.password'), Config::get('database.database'), $filedir . $filename], $command);
        echo $command;
        echo shell_exec($command);

        if (file_exists($filedir . $filename)) {
            // First, you need a filesystem adapter
            $adapter = new LocalAdapter($filedir);
            $filesystem = new Filesystem($adapter);

            header('Content-Description: File Transfer');
            header("Content-Type: " . $filesystem->mimeType($filename));
            header('Content-Disposition: attachment; filename="' . basename($filedir . $filename) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header("Content-Length: " . $filesystem->size($filename));

            echo readfile($filedir . $filename);

            $filesystem->delete($filename);
            exit;
        }
    } else {
        $w->out("Could not find backup command");
    }
}
