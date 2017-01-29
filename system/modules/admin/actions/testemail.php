<?php

function testemail_GET(Web $w) {
    $user = $w->Auth->user();
    if (!empty($user)) {
        $contact = $user->getContact();
        $w->Mail->sendMail($contact->email, $contact->email, "Test email", "Test email, ignore");
        $w->out("An email has been sent to your address");
    }
}