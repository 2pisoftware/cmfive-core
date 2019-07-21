<?php

function profile_image_GET(Web $w)
{
    $w->setLayout(null);
    $p = $w->pathMatch('id');

    $userId = $p['id'];
    
    if (!empty($userId)) {
        $user = $w->Auth->getUser($userId);
    } else {
        $user = $w->Auth->user();
    }
    if (!empty($user)) {
        $contact = $user->getContact();
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