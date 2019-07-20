<?php

function profileImage_GET(Web $w)
{
    $w->setLayout(null);
    $p = $w->pathMatch('login');

    $contactLogin = $p['login'];
    
    if (!empty($contactLogin)) {
        $user = $w->Auth->getUserForLogin($contactLogin);
        if (!empty($user)) {
            $contact = $user->getContact();
        }
    } else {
        $contact = $w->Auth->user()->getContact();
    }
    if (empty($contact)) {
        return;
    }
    header('Cache-control: public');
    
    if (empty($contact->profile_img)) {
        header('Content-type: image/png');
        $emailHash = md5(strtolower(trim($contact->email)));
        $w->out(file_get_contents('https://www.gravatar.com/avatar/' . $emailHash . '?d=identicon'));
    } else {
        header('Content-type: image/jpeg');
         $w->out(base64_decode($contact->profile_img));
    }
}