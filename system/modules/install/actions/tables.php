<?php

function tables_VAR()
{
    return array(
        ValidationService::add('db_database')->required()
    );
}

function tables_GET(Web $w)
{
    $installStep = $w->Install->getInstallStep('tables');
    
    //error_log("STEP: " . $installStep->getStep());
    //$w->ctx("step", $installStep->getStep());
    
    //'mysql:host=mysql1.alwaysdata.com;port=3306;dbname=xxx'
    if(empty($_SESSION['install']['saved']['db_username']))
    {
        $installStep->addError("A username is required to connect to a database when listing tables.");
    }
    else
    {
        try
        {
            $databases = $w->InstallDatabase->getDatabasesWithMigrations();
            
            //error_log("LIST DATABASES: " . print_r($databases, true));
            
            // save the array for use later
            $w->ctx('databases', $databases);
            
            if(empty($databases))
            {
                if(!$w->Install->isPassedRecently('create_user'))
                {
                    $installStep->addError("User \"" . $_SESSION['install']['saved']['db_username'] .
                                       "\" cannot access any databases.", 'warnings');
                }
            }
        }
        catch(Exception $e)
        {
            $installStep->addError("Couldn't retrieve databases for user \"" .
                                   $_SESSION['install']['saved']['db_username'] . "\" " . $e->getMessage());
        }
    }
}
    
function tables_POST(Web &$w) {
    
    $installStep = $w->Install->getInstallStep('tables');
    
    // if the plan is too refresh the databases, then don't try and save the database name or the new/existing bool
    if(!isset($_POST['db_source']) || strcmp($_POST['db_source'], 'refresh_databases') !== 0) {
        $installStep->saveInstall();
    }

    // figure out the success message to be displayed.
    $msg = '';
    if($installStep->isPassed('create_database'))
    {
        $msg = "Created database";
    }
    
    if($installStep->isPassed('ran_db_migration'))
    {
        $msg .= (empty($msg) ? "Ran" : " and ran") . " database table migration SQL";
    }
    
    if(empty($msg))
    {
        $msg = "Database selected";
    }
    
    // return success message
    return $msg;
}
    