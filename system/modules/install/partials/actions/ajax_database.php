<?php
    
// create a database, requires the root user.
function ajax_database_ALL(Web &$w)
{
    $installStep = $w->Install->getInstallStep('tables');
    
    try
    {
        if(!empty($_SESSION['install']['saved']['db_database']))
        {
            $rootPDO = $w->InstallDatabase->getRootPDO();
            
            $sql = "CREATE DATABASE `" . $_SESSION['install']['saved']['db_database'] . "`; " .
                    "GRANT ALL ON `" . $_SESSION['install']['saved']['db_database'] . "`.* " .
                    "TO '" . $_SESSION['install']['saved']['db_username'] .
                    "'@'" . $_SESSION['install']['saved']['db_host'] . "'; " .
                    "FLUSH PRIVILEGES;";
            
            $rootPDO->exec($sql);
           
            if($w->InstallDatabase->databaseExists($_SESSION['install']['saved']['db_database']))
            {
                $installStep->ranTest('create_database');
            }
            else
            {
                $installStep->ranTest('create_database', false);
                $installStep->addError("Database \"" . $_SESSION['install']['saved']['db_database'] .
                                       "\" was not created - unknown reason.");
            }
        }
        else
        {
            $installStep->ranTest('create_database', false);
            $installStep->addError("Please give your new database a name.");
        }
    }
    catch(Exception $e)
    {
        $installStep->ranTest('create_database', false);
        $installStep->addError("Error creating database \"" . $_SESSION['install']['saved']['db_database'] . "\"<br />" .
                               //$db_username . "@" . $url . "<br />" .
                               "* " . $e->getMessage());
    }
    
    
    // now run the table migrations
    if(!$installStep->isErrorOrWarning())
    {
        try
        {
            $w->InstallDatabase->importTables($w);
            $installStep->ranTest('ran_db_migration');
        }
        catch(Exception $e)
        {
            $installStep->ranTest('ran_db_migration', false);
            $installStep->addError("Error importing table sql into database \"" . $_SESSION['install']['saved']['db_database'] . "\"<br />" .
                                   //$db_username . "@" . $url . "<br />" .
                                   "* " . $e->getMessage());
        }
    }
}
