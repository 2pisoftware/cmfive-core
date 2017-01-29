<?php
    $installStep = $w->Install->getInstallStep('admin');
    if(!$_SESSION['install']['saved']['is_create_admin']):
    
        try
        {
            $admins = $w->InstallDatabase->getAdmins();
        }
        catch(Exception $e)
        {
            $installStep->addError("Couldn't retrieve admins for database \"" .
                $_SESSION['install']['saved']['db_database'] . "\" " . $e->getMessage());
        }
        
        if(!empty($admins)) :
?>
<b>USING EXISTING ADMIN USERS</b>
<ul>
<?php  foreach($admins as $admin) : ?>
    <li><?= $admin['login'] ?></li>
<?php  endforeach; ?>
</ul>
<?php
        else :
            $installStep->addError("Database \"" . $_SESSION['install']['saved']['db_database'] . "\" " .
                                    "does not have any admins.", 'warnings');
?>
<b class='error'>NO ADMIN USERS EXIST</b>
<?php
        endif;
    
    else:
        echo $installStep->getTestResultStr('create_admin', "CREATED ADMIN USER",
                                                        "FAILED TO CREATE ADMIN USER",
                                                        "ADMIN USER NOT CREATED");
?>
    <ul>
    <?php if(!empty($_SESSION['install']['saved']['admin_firstname']) || !empty($_SESSION['install']['saved']['admin_lastname'])) : ?>
        <li><b>Name:</b> <?= $_SESSION['install']['saved']['admin_firstname'] . ' ' . $_SESSION['install']['saved']['admin_lastname']; ?></li>
    <?php endif; ?>
    <?php if(!empty($_SESSION['install']['saved'][$_SESSION['install']['saved']['admin_email_type']])) : ?>
        <li><b>Email:</b> <?= $_SESSION['install']['saved'][$_SESSION['install']['saved']['admin_email_type']] ?></li>
    <?php endif; ?>
    <?php if(!empty($_SESSION['install']['saved']['admin_username'])) : ?>
        <li><b>User:</b> "<?= $_SESSION['install']['saved']['admin_username'] ?>"
                <?= (empty( $_SESSION['install']['saved']['admin_username']) ? "NO" : "WITH") . " PASSWORD" ?></li>
        <!--<li><b>Salt:</b> "<?= $_SESSION['install']['saved']['admin_salt'] ?>"</li>
        <li><b>Encrypted:</b> "<?= $_SESSION['install']['saved']['admin_encrypted'] ?>"</li>//-->
    <?php endif; ?>
    </ul>
<?php endif; ?>