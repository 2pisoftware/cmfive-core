<?php

// Using this as a guide: http://www.bendemeyer.com/2013/03/12/automated-site-backups-using-php-and-cron/
function databasebackup_ALL(Web $w)
{
    AdminService::getInstance($w)->navigation($w, "Database Backup");
    
    $datestamp = date("Y-m-d-H-i");
    $filedir = ROOT_PATH . "/backups/";
    
    $dir = new DirectoryIterator($filedir);
    foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot()) {
            $filename = $fileinfo->getFilename();
            try {
                $datepart = substr($filename, 0, strpos($filename, ".sql"));
                $backuptime = DateTime::createFromFormat("Y-m-d-H-i", $datepart);
                if ($backuptime) {
                    if ((time() - $backuptime->getTimestamp()) < (60*60*4)) {
                        $w->out("You cannot backup more than once every 4 hours");
                        return;
                    }
                }
            } catch (Exception $e) {
                // Invalid timestamp
            }
        }
    }
    
    $backupformat = Config::get('admin.database.output');
    $filename = "$datestamp.$backupformat";
    $command = null;
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $command = Config::get('admin.database.command.windows');
    } else {
        $command = Config::get('admin.database.command.unix');
    }
    if (!empty($command)) {
        $command = str_replace(
            array('$username', '$password', '$dbname', '$filename'),
            array(Config::get('database.username'), Config::get('database.password'), Config::get('database.database'), $filedir.$filename),
            $command
        );

        $w->out(shell_exec($command));
        $w->out("Backup completed to: {$filedir}{$filename}");
    } else {
        $w->out("Could not find backup command");
    }
}
