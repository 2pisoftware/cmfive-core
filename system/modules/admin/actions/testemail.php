<?php

function testemail_GET(Web $w) {
    $user = $w->Auth->user();
    if (!empty($user)) {
        $contact = $user->getContact();
        $w->Mail->sendMail($contact->email, $contact->email, __("Test email"), __("Test email, ignore"_);
        $w->out(__("An email has been sent to your address"));
    }
}
