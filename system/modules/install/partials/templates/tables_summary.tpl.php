<?php
    $installStep = $w->Install->getInstallStep('tables');

    if(empty($_SESSION['install']['saved']['db_database']))
    {
        echo '<span class="test failed">NO DATABASE</span>';
    }
    else
    {
        echo '"' . $_SESSION['install']['saved']['db_database'] . '" ' .
        
             $installStep->getTestResultStr('ran_db_migration',
                    // passed
                    $installStep->getTestResultStr('create_database', "DATABASE CREATED AND TABLES IMPORTED",
                                                                      "FAILED TO CREATE DATABASE",
                                                                      "IMPORTED TABLES"),
                                            
                    // failed
                    $installStep->getTestResultStr('create_database', "DATABASE CREATED BUT IMPORTING TABLES FAILED",
                                                                      "FAILED TO CREATE DATABASE",
                                                                      "FAILED TO IMPORT TABLES"),
                                            
                    // nothing ran
                    $installStep->getTestResultStr('create_database', "DATABASE CREATED",
                                                                      "FAILED TO CREATE DATABASE",
                                                                      ""));
    }
?>