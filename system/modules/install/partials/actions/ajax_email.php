<?php
    
function ajax_email_ALL(Web $w, $params)
{
    $installStep = $w->Install->getInstallStep('email');
    
    if(strcmp($_SESSION['install']['saved']['email_test_send'], 'no') === 0 ||
       empty($_SESSION['install']['saved']['email_test_send']))
    {
        $installStep->addError("Ajax called to send test email without request.", 'warnings');
        return;
    }
    
    $subject = "CmFive Test Email";

    $msg = <<<EOF
<html>
<head>
<title>$subject</title>
</head>
<body>
<h1>CmFive</h1>
<p>This is a test email to ensure the settings you provided for your SMTP outgoing email server are correct.</p>
<p>You can ignore this email.</p>
</body>
</html>
EOF;
        
    $installStep->ranTest('sent_test_email');

    if(!$installStep->isErrorOrWarning())
    {
        $email_to = $_SESSION['install']['saved'][ $_SESSION['install']['saved']['email_test_send'] ];
        $email_from = $_SESSION['install']['saved']['company_support_email'];
        
        if(empty($email_to))
        {
            $installStep->addError("Cannot send test email to an empty email address.");
        }
        else if(empty($email_from))
        {
            $installStep->addError("Cannot send test email from an empty email address.");
        }
        else
        {
            try
            {
                // Create the Transport
                if(strcmp($_SESSION['install']['saved']['email_transport'], "smtp") === 0)
                {
                    if(empty($_SESSION['install']['saved']['email_smtp_host']))
                    {
                        //$w->ctx("error", "Sending requested via smtp, but host not specified.");
                        $installStep->addError("Sending requested via smtp, but host not specified.");
                    }
                    else if(empty($_SESSION['install']['saved']['email_smtp_port']))
                    {
                        //$w->ctx("error", "Sending requested via smtp, but port not specified.");
                        $installStep->addError("Sending requested via smtp, but port not specified.");
                    }
                    else
                    {
                        $transport = Swift_SmtpTransport::newInstance($_SESSION['install']['saved']['email_smtp_host'],
                                                                  $_SESSION['install']['saved']['email_smtp_port']);
                    }
                }
                else if(strcmp($_SESSION['install']['saved']['email_transport'], "sendmail") === 0)
                {
                    if(empty($_SESSION['install']['saved']['email_sendmail']))
                    {
                        //$w->ctx("error", "Sending requested via sendmail, but command not specified.");
                        $installStep->addError("Sending requested via sendmail, but command not specified.");
                    }
                    else
                    {
                        $transport = Swift_SendmailTransport::newInstance($_SESSION['install']['saved']['email_sendmail']);
                    }
                }
                else
                    $transport = Swift_MailTransport::newInstance();
                
                if(isset($transport))
                {
                    // handle authentication
                    if($_SESSION['install']['saved']['email_auth'])
                    {
                        $transport->setUsername($_SESSION['install']['saved']['email_username'])
                                  ->setPassword($_SESSION['install']['saved']['email_password']);
                    }
                    
                    // handle encryption
                    $valid_encryptions = array('tls', 'ssl');
                    if(in_array($_SESSION['install']['saved']['email_encryption'], $valid_encryptions))
                    {
                        $transport->setEncryption($_SESSION['install']['saved']['email_encryption']);
                    }
                    
                    // Create the Mailer using your created Transport
                    $mailer = Swift_Mailer::newInstance($transport);
                    
                    // Create a message
                    $message = Swift_Message::newInstance($subject)
                        ->setFrom(array($email_from => 'CmFive'))
                        ->setTo(array($email_to))
                        ->setBody($msg, 'text/html', 'iso-8859-2');
                    
                    // Send the message
                    $result = $mailer->send($message);
                    
                    if($result != 1)
                    {
                        // it failed
                        $installStep->ranTest('sent_test_email', false);
                        
                        //$w->ctx("error", "Couldn't send test email to " . $email_to . "<br />" . $e->getMessage());
                        $installStep->addError("Couldn't send test email to " .
                                $email_to . "<br />" . $e->getMessage(), 'warnings');
                    }
                }
                else
                {
                    //$w->ctx("error", "Couldn't send test email via \"" . $_SESSION['install']['saved']['email_transport'] . "\"");
                    $installStep->addError("Couldn't send test email via \"" .
                                            $_SESSION['install']['saved']['email_transport'] . "\"", 'warnings');
                }
            }
            catch(Exception $e)
            {
                //$w->ctx("error", "Couldn't send test email!<br />" . $e->getMessage());
                $installStep->addError("Couldn't send test email!<br />" . $e->getMessage(), 'warnings');
            }
        }
    }

    echo $installStep->formatErrors('warnings') . $installStep->formatErrors();
}

