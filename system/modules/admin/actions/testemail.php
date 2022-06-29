<?php

function testemail_GET(Web $w) {
    $user = AuthService::getInstance($w)->user();
    if (!empty($user)) {
        $contact = $user->getContact();
        MailService::getInstance($w)->sendMail($contact->email, $contact->email, "Test email", "Test email, ignore");
        $w->out("An email has been sent to your address");
    }
}