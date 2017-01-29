<?php
    
function ajax_admin_ALL(Web $w, $params)
{
    $installStep = $w->Install->getInstallStep('admin');
    
    try
    {
        $pdo = $w->InstallDatabase->getPDO();
        
        if(empty($_SESSION['install']['saved']['admin_firstname']))
        {
            throw new Exception("Admin's first name must have a value");
        }
        
        if(empty($_SESSION['install']['saved']['admin_lastname']))
        {
            throw new Exception("Admin's last name must have a value");
        }
        
        if(empty($_SESSION['install']['saved']['admin_email']))
        {
            throw new Exception("Admin's email must have a value");
        }
        
        if(empty($_SESSION['install']['saved']['admin_username']))
        {
            throw new Exception("Admin's username must have a value");
        }
        
        // is an empty password is acceptable, even though it's a REALLY bad idea?
        $pdo->exec("USE " . $_SESSION['install']['saved']['db_database']);
        
        $unique_statement = $pdo->prepare("SELECT `id` FROM `user` WHERE `login` = ?;");
        $unique_statement->bindParam(1, $_SESSION['install']['saved']['admin_username']);
        $unique_statement->execute();
        if($unique_statement->rowCount())
        {
            throw new Exception("Admin username \"" . $_SESSION['install']['saved']['admin_username'] . "\" already exists");
        }
        
        // Create admin user
        $statement = $pdo->prepare("INSERT INTO contact (`id`, `firstname`, `lastname`, `othername`, `title`, `homephone`, `workphone`, `mobile`, `priv_mobile`, `fax`, `email`, `notes`, `dt_created`, `dt_modified`, `is_deleted`, `private_to_user_id`, `creator_id`) VALUES (NULL, ?, ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL, ?, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '0', NULL, NULL);");
        $statement->bindParam(1, $_SESSION['install']['saved']['admin_firstname']);
        $statement->bindParam(2, $_SESSION['install']['saved']['admin_lastname']);
        $statement->bindParam(3, $_SESSION['install']['saved']['admin_email']);
        $result = $statement->execute();
        
        $contact_id = $pdo->lastInsertId();
        
        $user_statement = $pdo->prepare("INSERT INTO user (`id`, `login`, `password`, `password_salt`, `contact_id`, `password_reset_token`, `dt_password_reset_at`, `redirect_url`, `is_admin`, `is_active`, `is_deleted`, `is_group`, `dt_created`, `dt_lastlogin`) VALUES (NULL, ?, ?, ?, ?, NULL, NULL, 'main/index', '1', '1', '0', '0', CURRENT_TIMESTAMP, NULL);");
        $user_statement->bindParam(1, $_SESSION['install']['saved']['admin_username']);
        
        // Generate encrypted password
        $salt = User::generateSalt();
        $_SESSION['install']['saved']['admin_salt'] = $salt;
            
        $password = sha1($salt . trim($_SESSION['install']['saved']['admin_password']));
        $_SESSION['install']['saved']['admin_encrypted'] = $password;
        
        $user_statement->bindParam(2, $password);
        $user_statement->bindParam(3, $salt);
        $user_statement->bindParam(4, $contact_id);
        $result = $user_statement->execute();
        
        $user_id = $pdo->lastInsertId();
        $role_statement = $pdo->prepare("INSERT INTO user_role (`id`, `user_id`, `role`) VALUES (NULL, ?, 'user');");
        $role_statement->bindParam(1, $user_id);
        $result = $role_statement->execute();

        $installStep->ranTest('create_admin');
    }
    catch(Exception $e)
    {
        $installStep->ranTest('create_admin', false);
        $installStep->addError("Error creating admin user<br />" .
                               //$db_username . "@" . $url . "<br />" .
                               "* " . $e->getMessage());
    }
}
