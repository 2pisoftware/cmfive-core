<?php
function email_VAR()
{
    return array(
        ValidationService::add('email_transport')->options(array('default', 'sendmail', 'smtp')),
        ValidationService::add('email_sendmail'),
        ValidationService::add('email_smtp_host'),
        ValidationService::add('email_smtp_port')->type('integer')->min(0)->max(65535),
        ValidationService::add('email_encryption')->options(array('none', 'ssl', 'tls')),
        ValidationService::add('email_auth')->type('boolean'),
        ValidationService::add('email_username'),
        ValidationService::add('email_password'),
        ValidationService::add('email_test')->type('email')->postName('email'),
        ValidationService::add('email_test_send')->options(array('no', 'company_support_email', 'email_test'))
    );
}

function email_POST(Web &$w) {
    $installStep = $w->Install->getInstallStep('email');
    
    // error checking and saving to session from $_POST
    $installStep->saveInstall(array('email_transport', 'email_auth', 'email_test_send',
                                                'email_smtp_host', 'email_smtp_port', 'email_encryption'));

    // if the email transport is sendmail, then the sendmail command is required.
    $installStep->getValidation('email_sendmail')->required(
        strcmp($_SESSION['install']['saved']['email_transport'], 'sendmail') === 0);

    // if the mta transport uses authentication, then require the username
    $installStep->getValidation('email_username')->required(
        $_SESSION['install']['saved']['email_auth']);

    // these fields are only ever used in ajax queries to send a test email
    if(strcmp($_SESSION['install']['saved']['email_test_send'], 'email_test') === 0 && isset($_POST['email_test']))
    {
        $installStep->saveInstall('email_test');
    }
    else if(strcmp($_SESSION['install']['saved']['email_test_send'], 'company_support_email') === 0 &&
            empty($_SESSION['install']['saved']['company_support_email']) && $w->isAjax())
    {
        // can't use company support email
        $installStep->addError("Trying to use company support email to send test, but it doesn't yet have a value");
    }
    else if(strcmp($_SESSION['install']['saved']['email_test_send'], 'admin_email') === 0 &&
        empty($_SESSION['install']['saved']['admin_email']) && $w->isAjax())
    {
        // can't use company support email
        $installStep->addError("Trying to use admin email to send test, but it no admin was recently created");
    }
    
    $installStep->saveInstall(array('email_sendmail', 'email_username', 'email_password'));
    
    // success message
    return "Configured Email Sending";
}
