<?php

function ajax_import_ALL(Web &$w)
{
    $installStep = $w->Install->getInstallStep('tables');

    try
    {
        $w->InstallDatabase->importTables($w);
        $installStep->ranTest('ran_db_migration');
    }
    catch(Exception $e)
    {
        $installStep->ranTest('ran_db_migration', false);
        $installStep->addError("Error importing table sql into database \"" . $db_database . "\"<br />" .
                               //$db_username . "@" . $url . "<br />" .
                               "* " . $e->getMessage());
    }
}
