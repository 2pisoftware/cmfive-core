<?php

/** 
 * holds email data for bulk emails 
 *
 * @author IsaacLynnah <isaac@2pisoftware.com> Aug 2016
 */

class MailQueue extends DbObject {
    
    public $to_contact_id;
    public $batch_id;
    
    // standard system properties
	
    public $is_deleted; // <-- is_ = tinyint 0/1 for false/true
    public $dt_created; // <-- dt_ = datetime values
    public $dt_modified;
    public $modifier_id; // <-- foreign key to user table
    public $creator_id; // <-- foreign key to user table
	
    public function getRecipient() {
        return $this->getObject('contact', $this->to_contact_id);
    }
    
    
    public function send() {
        $batch = $this->w->mail->getBatchForId($this->batch_id);
        if(empty($batch)) {
            
            throw new Exception('No batch found.');
        }
        $to_contact = $this->w->mail->getObject('contact', $this->to_contact_id);
        if (empty($to_contact)) {
            throw new Exception('No contact found for reciever id.');
        }       
        $from_contact = $this->w->auth->getUser($batch->user_to_notify)->getContact();
//        $this->w->Log->debug(" bulk email from contact: " . var_dump($from_contact) . ' ' . print_r($from_contact));
        if (empty($from_contact)) {
            throw new Exception('No contact found for sender id');
        }      
        $attachments = $this->w->File->getAttachmentsFileList('mail_batch', $this->batch_id);
        $data_array = [];
        $data_array['contact'] = $to_contact->toArray();
        $data_array['sender'] = $from_contact->toArray();
        if (!empty($batch->message)) {
            $data_array['message'] = $batch->message;
        }
        if (!empty($this->extra_data)) {
            $data_array['data'] = $batch->extra_data;
        }
        $message = $this->w->Template->render($batch->template_id, $data_array);
       
        $this->w->Mail->sendMail(
            $to_contact->email, 
            Config::get('main.company_support_email'),
            $batch->subject, 
            $message, 
            null, 
            null, 
            $attachments
        );
    }
}