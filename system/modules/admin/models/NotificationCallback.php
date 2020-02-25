<?php

/**
 * Holds data from processing the NotificationService sendToAllWithCallback's
 * callback.
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
class NotificationCallback
{
    public $recipient_user;
    public $template_data;
    public $attachments;

    /**
     * Constructor method
     *
     * @param User $recipient_user
     * @param array $template_data
     * @param array $attachments
     */
    public function __construct($recipient_user = null, $template_data = [], $attachments = [])
    {
        $this->recipient_user = $recipient_user;
        $this->template_data = $template_data;
        $this->attachments = $attachments;
    }
}
