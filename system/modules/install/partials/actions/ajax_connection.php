<?php
    
function ajax_connection_ALL(Web $w, $params)
{
    $installStep = $w->Install->getInstallStep('database');
    
    try
    {
        $pdo = $w->InstallDatabase->getPDO();
        $installStep->ranTest('check_connection');
    }
    catch(Exception $e)
    {
        $installStep->ranTest('check_connection', false);
        $installStep->addError("Error checking connection<br />" .
                     //$_SESSION['install']['saved']['db_username'] . "@" . $w->InstallDatabase->getURL() .
                     //          " " . (empty( $_SESSION['install']['saved']['db_password']) ? "NO" : "USING") . " PASSWORD<br />" .
                    "* " . $e->getMessage());
    }
}
    
 
