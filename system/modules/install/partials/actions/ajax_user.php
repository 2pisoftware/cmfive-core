<?php

// creates a new user
function ajax_user_ALL(Web $w, $params)
{
    $installStep = $w->Install->getInstallStep('database');
    
    if(empty($_SESSION['install']['saved']['db_username']))
    {
        $installStep->addError("Cannot create a new user without a username.");
    }
    else
    {
        try
        {
            /*
             From phpmyadmin:
             CREATE USER 'somebody'@'localhost' IDENTIFIED BY '***';GRANT USAGE ON *.* TO 'somebody'@'localhost' IDENTIFIED BY '***' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;
             */
            $rootPDO = $w->InstallDatabase->getRootPDO();
            $sql = "CREATE USER `" . $_SESSION['install']['saved']['db_username'] .
                        "`@'" . $_SESSION['install']['saved']['db_host'] .
                        "' IDENTIFIED BY '" . $_SESSION['install']['saved']['db_password'] . "'; FLUSH PRIVILEGES;";
            $rootPDO->exec($sql);
            $installStep->ranTest('create_user');
        }
        catch(Exception $e)
        {
            $installStep->ranTest('create_user', false);
            $installStep->addError("Could not create new database user.<br/>\n" . $e->getMessage());
        }
        
        // now check to make sure we can connect using that user
        try
        {
            if(!$installStep->isErrorOrWarning())
            {
                $pdo = $w->InstallDatabase->getPDO();
                $installStep->ranTest('check_connection');
            }
        }
        catch(Exception $e)
        {
            $installStep->ranTest('check_connection', false);
            $installStep->addError("Error checking connection<br />" .
                                   $_SESSION['install']['saved']['db_username'] . "@" . $w->InstallDatabase->getURL() . " " .
                                   (empty($_SESSION['install']['saved']['db_password']) ? "NO" : "USING") . " PASSWORD<br />" .
                                   "* " . $e->getMessage());
        }
    }
}
