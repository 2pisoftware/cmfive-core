<?php
// Use Webauthn to authenticate a device
function device_GET(Web $w)
{
    // Check if logged in already
    $user = AuthService::getInstance($w)->user();
    if (AuthService::getInstance($w)->loggedIn() && AuthService::getInstance($w)->allowed($user->redirect_url)) {
        $w->redirect($w->localUrl(!empty($user->redirect_url) ? $user->redirect_url : "/main"));
    }

    
}