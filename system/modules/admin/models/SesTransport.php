<?php

/**
 * Transport implementation for the swiftmailer library
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class SesTransport implements GenericTransport
{
    private $w;
    private $transport;

    public function __construct($w, $layer)
    {
        $this->w = &$w;
        $this->transport = $this->getTransport($layer);
    }

    public function getTransport($layer)
    {
        if (!empty($this->transport)) {
            return $this->transport;
        }

        switch (strtolower($layer)) {
            case "ses":
                $host = Config::get('email.host');
                $port = Config::get('email.port');
                $encryption = Config::get('email.encryption', Config::get('email.auth') == true ? 'ssl' : null);

                $username = Config::get('email.username');
                $password = Config::get('email.password');

                $transportObject = new Swift_SmtpTransport($host, $port, $encryption);

                // in case we don't have these details, don't assert
                // (assume IAM etc will resolve)
                if (!empty($username) && !empty($password)) {
                    $transportObject
                    ->setUsername($username)
                    ->setPassword($password);
                }
                return ($transportObject);
                break;
            default:
        }
    }

    public function send($to, $replyto, $subject, $body, $cc = null, $bcc = null, $attachments = [], $headers = [])
    {
        if (!empty($to) && strlen($to) > 0) {
            try {
                if ($this->transport === null) {
                    LogService::getInstance($this->w)->error("Could not send mail to {$to} from {$replyto} about {$subject} no email transport defined!");
                    return;
                }

                $mailer = new Swift_Mailer($this->transport);

                // To, cc, bcc need to be given as arrays when sending to more than one person
                // Ie you separate them by a comma, this will split them into arrays as expected by Swift
                if (strpos($to, ",") !== false) {
                    $to = array_map("trim", explode(',', $to));
                }

                // Create message
                $message = new Swift_Message($subject);
                $fromCompany = Config::get("main.company_support_email");
                if (empty($fromCompany)) {
                    LogService::getInstance($this->w)->error("Failed to send mail to: {$to}, from: {$replyto}, about: {$subject}: main.company_support_email not set in config");
                    return;
                }

                $message->setFrom($fromCompany)
                    ->setTo($to)->setBody($body)
                    ->addPart($body, 'text/html');

                if (is_array($replyto)) {
                    $message->setReplyTo($replyto);
                } else {
                    $message->setReplyTo([$replyto]);
                }
                if (!empty($cc)) {
                    if (strpos($cc ?? "", ",") !== false) {
                        $cc = array_map("trim", explode(',', $cc));
                    }
                    $message->setCc($cc);
                }
                if (!empty($bcc)) {
                    if (strpos($bcc ?? "", ",") !== false) {
                        $bcc = array_map("trim", explode(',', $bcc));
                    }
                    $message->setBcc($bcc);
                }

                // Add attachments
                if (!empty($attachments)) {
                    foreach ($attachments as $attachment) {
                        if (!empty($attachment)) {
                            $message->attach(Swift_Attachment::fromPath($attachment));
                        }
                    }
                }

                // Set any extra headers
                if (!empty($headers)) {
                    foreach ($headers as $header => $value) {
                        LogService::getInstance($this->w)->setLogger(MailService::$logger)->info("Added header {$header} {$value}");
                        $message->getHeaders()->addTextHeader($header, $value);
                    }
                }
                LogService::getInstance($this->w)->setLogger(MailService::$logger)->info("Sending email to {$to} from {$replyto} with {$subject} (" . count($attachments) . " attachments)");
                $mailer_status = $mailer->send($message, $failures);
                if (!empty($failures)) {
                    LogService::getInstance($this->w)->setLogger(MailService::$logger)->error("Failed to send email: " . serialize($failures));
                }
            } catch (Exception $e) {
                LogService::getInstance($this->w)->setLogger(MailService::$logger)->error("Failed to send email: " . $e);
            }
            // failure to end
            return 1;
        }
    }
}
