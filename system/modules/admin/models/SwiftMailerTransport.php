<?php

/**
 * Transport implementation for the swiftmailer library
 * 
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class SwiftMailerTransport implements GenericTransport {
	private $w;
	private $transport;
	
	public function __construct($w, $layer) {
		$this->w = &$w;
		$this->transport = $this->getTransport($layer);
	}
	
	public function getTransport($layer) {
		if (!empty($this->transport)) {
			return $this->transport;
		}
		
		switch(strtolower($layer)) {
			case "smtp":
			case "swiftmailer":
				if (method_exists('Swift_SmtpTransport', 'newInstance')) {
					return Swift_SmtpTransport::newInstance(Config::get('email.host'), Config::get('email.port'), Config::get('email.auth') == true ? 'ssl' : null)
						->setUsername(Config::get('email.username'))
						->setPassword(Config::get('email.password'));
				} else {
					return (new Swift_SmtpTransport(Config::get('email.host'), Config::get('email.port'), Config::get('email.auth') == true ? 'ssl' : null))
						->setUsername(Config::get('email.username'))
						->setPassword(Config::get('email.password'));
				}
			break;
			case "sendmail":
				$command = Config::get('email.command');
				if (!empty($command)) {
					if (method_exists('Swift_SendmailTransport', 'newInstance')) {
						return Swift_SendmailTransport::newInstance($command);
					} else {
						return new Swift_SendmailTransport($command);
					}
				} else {
					if (method_exists('Swift_SendmailTransport', 'newInstance')) {
						return Swift_SendmailTransport::newInstance();
					} else {
						return new Swift_SendmailTransport();
					}
				}
			break;
			default:
				
		}
	}

	public function send($to, $replyto, $subject, $body, $cc = null, $bcc = null, $attachments = array()) {
		if (!empty($to) && strlen($to) > 0) {
			try {
				if ($this->transport === NULL) {
					$this->w->Log->error("Could not send mail to {$to} from {$replyto} about {$subject} no email transport defined!");
					return;
				}
				
				// if (filter_var($to, FILTER_VALIDATE_EMAIL) === false) {
				// 	$this->w->Log->error("Email address: {$to} is invalid");
				// 	return;
				// }
				
				
				$mailer = null;
				if (method_exists('Swift_Mailer', 'newInstance')) {
					$mailer = Swift_Mailer::newInstance($this->transport);
				} else {
					$mailer = new Swift_Mailer($this->transport);
				}
				// To, cc, bcc need to be given as arrays when sending to more than one person
				// Ie you separate them by a comma, this will split them into arrays as expected by Swift
				if (strpos($to, ",") !== FALSE) {
					$to = array_map("trim", explode(',', $to));
				}
				
				// Create message
				$message = null;
				if (method_exists('Swift_Message', 'newInstance')) {
					$message = Swift_Message::newInstance($subject);
				} else {
					$message = new Swift_Message($subject);
				}
				
				$message->setFrom($replyto)
						->setTo($to)->setBody($body)
						->addPart($body, 'text/html');
				
				if (is_array($replyto)) {
					$message->setReplyTo($replyto);
				} else {
					$message->setReplyTo(array($replyto));
				}
				if (!empty($cc)) {
					if (strpos($cc, ",") !== FALSE) {
						$cc = array_map("trim", explode(',', $cc));
					}
					$message->setCc($cc);
				}
				if (!empty($bcc)) {
					if (strpos($bcc, ",") !== FALSE) {
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

				$this->w->Log->setLogger(MailService::$logger)->info("Sending email to {$to} from {$replyto} with {$subject} (" . count($attachments) . " attachments)");
				$mailer_status = $mailer->send($message, $failures);
				if (!empty($failures)) {
					$this->w->Log->setLogger(MailService::$logger)->error("Failed to send email: " . serialize($failures));
				}
			} catch (Exception $e) {
				$this->w->Log->setLogger(MailService::$logger)->error("Failed to send email: " . $e);
			}
			// failure to end
			return 1;
		}
	}

}
