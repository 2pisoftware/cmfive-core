<?php

/**
 * holds email batch data for bulk emails
 *
 * @author IsaacLynnah <isaac@2pisoftware.com> Aug 2016
 */
class MailBatch extends DbObject
{

    public $status;
    public $details;
    public $dt_started;
    public $dt_finished;
    public $user_to_notify;
    public $subject;
    public $title;
    public $message;
    public $template_id;
    public $tag;
    public $is_main_contact;
    public $is_billing_contact;
    public $is_self;
    public $extra_data;
    public $number_sent;

    // standard system properties
    public $is_deleted; // <-- is_ = tinyint 0/1 for false/true
    public $dt_created; // <-- dt_ = datetime values
    public $dt_modified;
    public $modifier_id; // <-- foreign key to user table
    public $creator_id; // <-- foreign key to user table

    //notify user on completion
    public function completed()
    {
        $this->dt_finished = time();
        $subject = 'Batch: ' . $this->subject . ". Has been completed";
        $message = '<p><b>Email batch completed @:</b> ' . date("y/m/d H:i:s", $this->dt_finished) . "<p>";
        $message .= "<p><b>Number of emails sent:</b> " . $this->number_sent . "</p>";
        $message .= "<p><b>Bulk send details:</b> ";
        if (empty($this->details)) {
            $message .= 'No errors to report';
        } else {
            $message .= 'This batch encounted these issues: <br>' . $this->details;
        }
        $message .= "</p>";
        MailService::getInstance($this->w)->sendMail(
            AuthService::getInstance($this->w)->getUser($this->user_to_notify)->getContact()->email,
            Config::get('main.company_support_email'),
            $subject,
            $message,
            null,
            null,
            null
        );
        $this->status = 'Completed';
        $this->update();
    }
}
