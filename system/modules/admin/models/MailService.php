<?php

class MailService extends DbService {
	
	private $transport;
	public static $logger = 'MAIL';
        
	public function __construct(Web $w) {
		parent::__construct($w);
		$this->initTransport();
		$this->w->Log->setLogger(MailService::$logger)->info("Initialised transport: " . get_class($this->transport));
	}
	
	public function getTransport() {
		return $this->transport;
	}
	
    /**
     * Sends an email using config array from /config.php and the swiftmailer lib
     * for transport. 
     * 
     * @param string $to
     * @param string $replyto
     * @param string $subject
     * @param string $body
     * @param string $cc (optional)
     * @param string $bcc (optional)
     * @param Array $attachments (optional)
     * @return int
     */
    public function sendMail($to, $replyto, $subject, $body, $cc = null, $bcc = null, $attachments = array()) {
		$this->w->Log->setLogger(MailService::$logger)->info("Sending email to " . $to);
        if (!empty($this->transport)) {
			$this->transport->send($to, $replyto, $subject, $body, $cc, $bcc, $attachments);
		} else {
			$this->w->Log->setLogger(MailService::$logger)->error("Transport layer not found");
		}
    }

    private function initTransport() {
        $layer = Config::get('email.layer');
        
        // Set default layer if it doesn't exist
        if (empty($layer)) {
            $layer = "sendmail";
        }
        
		$transport = Config::get('email.transports.' . $layer);
		
		if (class_exists($transport) && array_key_exists("GenericTransport", class_implements($transport))) {
			$this->w->Log->setLogger(MailService::$logger)->info("Loading " . $layer . " transport");
			$this->transport = new $transport($this->w, $layer);
		} else {
			$this->w->Log->setLogger(MailService::$logger)->error("Transport class " . $transport . " does not exist or does not implement GenericTransport");
		}
		
    }
    
    /**
     * @return string current email batch id
     */
    public function getCurrentBatchId () {
        return $this->_db->sql('SELECT id FROM mail_batch WHERE is_deleted = 0 '
                . 'AND status = "Active" LIMIT 1')->fetch_row();
    }
    
    /**
     * 
     * @return object MailBatch
     */
    public function getBatchForId($batch_id) {
        return $this->getObject('MailBatch', $batch_id);
    }
    
    /**
     * 
     * @return array emailbatch
     */
    public function getAllEmailBatches () {
        return $this->getObjects('MailBatch', ['is_deleted'=>0]);
    }
    
    /**
     * 
     * @param Integer $batch_id
     * @param Integer $number number of queued emails to return 
     * @return array of EmailQueue ids
     */
    public function getNextEmailsForBatch($batch_id, $number) {
        return $this->_db->sql('SELECT id FROM mail_queue WHERE batch_id = ' . $batch_id . ' AND is_deleted = 0 ORDER BY dt_created LIMIT ' . $number)->fetch_all();
    
    }
    
    /**
     * 
     * @param Integer $email_id 
     * @return obj MailQueue
     */
    public function getQueueObjForId($email_id) {
        return $this->getObject('MailQueue', $email_id);
    }
}
