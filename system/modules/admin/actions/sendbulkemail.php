<?php

/*
 * sends emails from queue via cron
 * 
 * @author Isaac Lynnah isaac@2pisoftware.com Aug 2016
 */ 

function sendbulkemail_ALL (Web $w) {
    //force authentication for getting attachments
    AuthService::getInstance($w)->forceLogin(Config::get('admin.bulkemail.auth_user'));
    //get current batch id
    $batch_id = MailService::getInstance($w)->getCurrentBatchId();
    
    if (!empty($batch_id)) {
        $batch = MailService::getInstance($w)->getBatchForId($batch_id['id']);
        $emails = MailService::getInstance($w)->getNextEmailsForBatch($batch_id['id'],Config::get('admin.bulkemail.number_per_cron'));
        if (empty($emails)) {
            //batch is finished
            $batch->completed();
        } else {
            //send emails
            foreach ($emails as $email_id) {
                //print_r($email_id);
                $email = MailService::getInstance($w)->getQueueObjForId($email_id['id']);
                if (!empty($email)) {
                    try {
                        $email->send();
                        $email->delete();
                        $batch->number_sent += 1;
                    } catch (Exception $ex) {
                        $contact = $email->getRecipient();
                        $email->delete();
                        $batch->details .= "Error for email: " . $email->id . ". Recipient: " . $contact->getFullName() . ". Message: " . $ex->getMessage() . "<br>";
                    }
                } else {
                    //could not find email for id logg in batch details
                    $batch->details .= "Error: No queue object found for id: " . $email_id['id'] . "<br>";
                }
            }
            $batch->Update();
        }
    }
    
}

