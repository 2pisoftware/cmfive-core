<?php

function admin_VAR()
{    
    return array(
        ValidationService::add('admin_firstname')->postName('fname'),
        ValidationService::add('admin_lastname')->postName('lname'),
        ValidationService::add('admin_email_type')->options('company_support_email', 'admin_email'),
        ValidationService::add('admin_email')->type('email')->postName('email'),
        ValidationService::add('admin_username')->postName('username'),
        ValidationService::add('admin_password')->postName('password'),
        ValidationService::add('admin_salt'),
        ValidationService::add('admin_encrypted'),
        ValidationService::add('is_create_admin')->type('boolean')->defaultValue(false)
    );
}

function admin_GET(Web $w) {
    $installStep = $w->Install->getInstallStep('admin');
    //$installStep->clearInstallErrors(); // has to be cleared before partial templates are displayed
    
    //error_log("STEP: " . $installStep->getStep());
    //$w->ctx("step", $installStep->getStep());
    
    //'mysql:host=mysql1.alwaysdata.com;port=3306;dbname=xxx'
    if(empty($_SESSION['install']['saved']['db_username']))
    {
        $installStep->addError("A username is required to connect to a database when listing admins.");
    }
    else if(empty($_SESSION['install']['saved']['db_database']))
    {
        $installStep->addError("A database is required when listing admins.");
    }
    else
    {
        try
        {
            $admins = $w->InstallDatabase->getAdmins();
            if(!empty($admins))
            {
                // save the array for use later
                $w->ctx('admins', $admins);
            }
            else
            {
                if(!$w->Install->isPassedRecently('create_database'))
                {
                    $installStep->addError("Database \"" . $_SESSION['install']['saved']['db_database'] .
                                       "\" does not have any admins.", 'warnings');
                }
            }
        }
        catch(Exception $e)
        {
            $installStep->addError("Couldn't retrieve admins for database \"" .
                                   $_SESSION['install']['saved']['db_database'] . "\" " . $e->getMessage());
        }
    }
}

function admin_POST(Web &$w) {
    $installStep = $w->Install->getInstallStep('admin');
    $installStep->saveInstall('is_create_admin');
    
    //error_log("POST: " . print_r($_POST, true));
    error_log("CREATE ADMIN: " . ($_SESSION['install']['saved']['is_create_admin'] ? 'true' : 'false'));

    if(!$_SESSION['install']['saved']['is_create_admin'])
    {
        return "Using existing admin users";
    }
    else
    {
        // error checking and saving to session
        $installStep->saveInstall(array('admin_firstname', 'admin_lastname', 'admin_email_type',
                                                    'admin_username', 'admin_password'));
        
        if(strcmp($_SESSION['install']['saved']['admin_email_type'], 'company_support_email') === 0)
        {
            $installStep->getValidation('admin_email')->required(false);
            
            // if there is no company support email
            if(empty($_SESSION['install']['saved']['company_support_email']))
            {
                $installStep->addError("Trying to use company support email as admin email, but it doesn't yet have a value");
            }
            else
            {
                // override the admin email
                $_SESSION['install']['saved']['admin_email'] = $_SESSION['install']['saved']['company_support_email'];
            }
        }
        else
        {
            $installStep->getValidation('admin_email')->required();
            $installStep->saveInstall('admin_email');
        }
        
        // return the success message if everything was hunky-dory
        return "Configured Admin User";
    }
}

