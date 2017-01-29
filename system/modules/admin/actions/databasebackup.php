<?php

// Using this as a guide: http://www.bendemeyer.com/2013/03/12/automated-site-backups-using-php-and-cron/
function databasebackup_ALL(Web $w) {
    $w->Admin->navigation($w, "Database Backup");
    
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
    $command = NULL;
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $command = Config::get('admin.database.command.windows');
    } else {
        $command = Config::get('admin.database.command.unix');
    }
    if (!empty($command)) {
        $command = str_replace(
                array('$username', '$password', '$dbname', '$filename'), 
                array(Config::get('database.username'), Config::get('database.password'), Config::get('database.database'), $filedir.$filename), 
                $command);
        $w->out(shell_exec($command));
        $w->out("Backup completed to: {$filedir}{$filename}");
        
        // Save elsewhere if defined
        $backuplocations = Config::get('admin.database.backuplocations');
        if (!empty($backuplocations)) {
            foreach($backuplocations as $location => $data) {
                $adapter = null;
                // Create adapter
                switch($location) {
                    case 'dropbox':
                        // Dropbox requires the OAuth extension
                        if (!class_exists("OAuth")) {
                            $w->out("You need the OAuth extension installed to backup to dropbox");
                            continue;
                        }
                            
                        $dropboxapi = new Dropbox_API(new Dropbox_OAuth_PHP($data['key'], $data['secret']));
                        $dropboxadapter = new Gaufrette\Adapter\Dropbox($dropboxapi);
                        $adapter = new \Gaufrette\Adapter($dropboxadapter);
                        break;
                }
                
                // Use adapter to save to external source
                if (!empty($adapter)) {
                    $filesystem = new Gaufrette\Filesystem($adapter);
                    $filesystem->write($filedir.$filename, file_get_contents($filedir.$filename));
                }
            }
        }
    } else {
        $w->out("Could not find backup command");
    }
}
