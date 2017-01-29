<?php    // transport and/or smtp server
    $installStep = $w->Install->getInstallStep('email');

    if(empty($_SESSION['install']['saved']['email_transport']))
        echo 'DEFAULT MTA ';
    else if(strcmp($_SESSION['install']['saved']['email_transport'], 'sendmail') === 0)
        echo 'SENDMAIL';
    else if(strcmp($_SESSION['install']['saved']['email_transport'], 'smtp') === 0)
        echo 'SMTP';
    
    echo " " . $installStep->getTestResultStr('sent_test_email', "SENT TEST EMAIL",
                                                                 "FAILED",
                                                                 "UNTESTED");
?>
<ul>
<?php
    if(strcmp($_SESSION['install']['saved']['email_transport'], 'sendmail') === 0)
        echo '<li><b>Command:</b> `' . $_SESSION['install']['saved']['email_sendmail']  . '`</li>';
    if(strcmp($_SESSION['install']['saved']['email_transport'], 'smtp') === 0)
        echo '<li><b>SMTP host:</b> "' . $_SESSION['install']['saved']['email_smtp_host'] . ':' .
    $_SESSION['install']['saved']['email_smtp_port'] . '"</li>';
?>
    <li><b>Encryption:</b> <?php
    if(empty($_SESSION['install']['saved']['email_encryption']))
        echo 'NO ENCRYPTION ';
    else if(strcmp($_SESSION['install']['saved']['email_encryption'], 'ssl') === 0)
        echo 'Secure Sockets Layer (SSL)';
    else if(strcmp($_SESSION['install']['saved']['email_encryption'], 'tls') === 0)
        echo 'Transport Layer Security (TLS)';
?></li>
<?php if($_SESSION['install']['saved']['email_auth'] && !empty($_SESSION['install']['saved']['email_username'])) : ?>
    <li><b>Authentication:</b> <?php
    if(!$_SESSION['install']['saved']['email_auth'])
        echo "NONE";
    else
        echo '"' . $_SESSION['install']['saved']['email_username'] . '" ' .
    (empty($_SESSION['install']['saved']['email_password']) ? "NO" : "USING") . " PASSWORD"; ?></li>
<?php endif; ?>
</ul>
