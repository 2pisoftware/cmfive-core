<?php

class MandrillTransport implements GenericTransport {
	private $w;
	private $transport;
	
	public function __construct($w, $layer) {
		$this->w = &$w;
		$this->transport = $this->getTransport($layer);
	}
	
	public function getTransport($layer) {
		switch(strtolower($layer)) {
			case "mandrill" :
				if (class_exists('Mandrill')) {
					return new Mandrill(Config::get("email.password"));
				} else {
					$this->w->Log->setLogger(MailService::$logger)->error("Class Mandrill doesn't exist, have you run a Composer update?");
				}
				break;
			default:
				$this->w->Log->setLogger(MailService::$logger)->error("Layer " . $layer . " is not supported by MandrillTransport");
		}
	}

	public function send($to, $replyto, $subject, $body, $cc = null, $bcc = null, $attachments = array()) {
		if (!empty($to) && strlen($to) > 0) {
			try {
				if ($this->transport === NULL) {
					$this->w->Log->setLogger(MailService::$logger)->error("Could not send mail to {$to} from {$replyto} about {$subject} no email transport defined!");
					return;
				}
				
				// Create message structure
				$message = array(
					'html' => $body,
					'subject' => $subject,
					'headers' => array('Reply-To' => $replyto),
					'auto_text' => true,
					'to' => array(),
					'attachments' => array()
				);
				
				// Set 'to' field
				if (is_scalar($to)) {
					// If scalar, look for ',' meaning multiple recipients given as a string
					if (strpos($to, ",") !== FALSE) {
						foreach(array_map("trim", explode(',', $to)) as $to_email) {
							$message['to'][] = array(
								'email' => $to_email,
								'name' => $to_email,
								'type' => 'to'
							);
						}
					} else {
						// Assume only one value given (as string)
						$message['to'] = array(
							array(
								'email' => $to,
								'name' => $to,
								'type' => 'to'
							)
						);
					}
				} else if (is_array($to)) {
					// If to given as array, assume its in the form of [email => recipient name]
					foreach($to as $to_email => $to_name) {
						$message['to'][] = array(
							'email' => $to_email,
							'name' => $to_name,
							'type' => 'to'
						);
					}
				} else {
					// If we get here then no acceptible format was given, log and return
					$this->w->Log->setLogger(MailService::$logger)->error("Cannot send email: 'to' expects to be a string or array, " . gettype($to) . " given");
					return;
				}
				
				// Set reply to
				if (is_array($replyto)) {
					foreach($replyto as $replyto_email => $replyto_name) {
						$message['from_email'] = $replyto_email;
						$message['from_name'] = $replyto_name;
						break;
					}
				} else {
					$message['from_email'] = $replyto;
					$message['from_name'] = $replyto;
				}
				
				// Set cc and bcc
				if (!empty($cc)) {
					$message['cc_address'] = $cc;
				}
				if (!empty($bcc)) {
					$message['bcc_address'] = $bcc;
				}
			
				// Set attachments
				if (!empty($attachments)) {
					foreach($attachments as $attachment) {
						if (is_scalar($attachment)) {
							$finfo = finfo_open(FILEINFO_MIME_TYPE);
							
							if ($file = file_exists($attachment)) {
								$message['attachments'][] = array(
									'type' => finfo_file($finfo, $attachment),
									'name' => basename($attachment),
									'content' => file_get_contents($attachment)
								);
							} else {
								// Assume the string given is the contents of a file
								$message['attachments'][] = array(
									'type' => $finfo->buffer($attachment),
									'name' => 'attachment',
									'content' => $attachment
								);
							}
						} else {
							// Assume its in the required mandrill format
							$message['attachments'][] = $attachment;
						}
					}
				}
				
				$result = $this->transport->messages->send($message, false);
				$this->w->Log->setLogger(MailService::$logger)->info(json_encode($result));
			} catch (Exception $e) {
				$this->w->Log->setLogger(MailService::$logger)->error("Failed to send email: " . $e);
				$this->w->Log->setLogger(MailService::$logger)->error(ini_get('curl.cainfo'));
			}
		} else {
			$this->w->Log->setLogger(MailService::$logger)->error("Empty data given to send");
		}
	}

}