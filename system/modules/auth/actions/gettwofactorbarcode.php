<?php

function gettwofactorbarcode_GET(Web &$w) {
    if (!$w->Auth->loggedIn()) return;

    $g = new \Google\Authenticator\GoogleAuthenticator();
    $secret = $g->generateSecret();

    $user = $w->Auth->user();
    $user->secret_2fa = $secret;
    $user->update();

    $w->ctx("barcode", '<img src="'.$g->getURL($user->login, $_SERVER['HTTP_HOST'], $secret).'" />');
}