<?php

/**
 * This action kicks off the Data Migration classes for all modules
 * 
 * In the future, there should be a mechanism to prevent un-authorised access.
 * 
 * For now you will have to disable the action in the config file.
 * 
 */

// Why supress warnings when using require?
require_once ROOT_PATH . "/system/classes/DbMigration.php";

function datamigration_GET(Web $w) {
    $modules = Config::keys();
    
}