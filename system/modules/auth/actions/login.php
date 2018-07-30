<?php

function login_GET(Web $w) {
    // Check if logged in already
    if ($w->Auth->loggedIn()) {
        $user = $w->Auth->user();

        if ($w->Auth->allowed($user->redirect_url))
            $w->redirect($w->localUrl(!empty($user->redirect_url) ? $user->redirect_url : "/main"));
    }

    $w->ctx("w", $w);

    // 2 factor authentication
    //$w->session("current_get_time", time());
}

function login_POST(Web &$w) {
    if ($w->session('2fa') == "disabled") {
        if (!$w->request('login') || !$w->request('password')) {
            $w->error("Please enter your login and password", "/auth/login");
        }
    
        $user = $w->Auth->login($w->request('login'), $w->request('password'), "Australia/Sydney");
    
        if (empty($user) && $w->session('2fa') == "disabled") {
            $w->error("Login or Password incorrect", "/auth/login");
        }

        else if (empty($user) && $w->session('2fa') == "enabled") {
            $w->redirect("/auth/login");
        }
    }

    // 2-factor authentication
    else if ($w->session('2fa') == "enabled") {
        /*$current_submit_time = time();
        $current_get_time = $this->w->session("current_get_time");
        $diff = $current_submit_time - $current_get_time;
        if ($diff > 60) {
            return null;
        }*/
    
        $user = $w->Auth->getUser($w->session('2fa_user_id'));

        $g = new \Google\Authenticator\GoogleAuthenticator();
        $secret = $user->secret_2fa;
        $code = $w->request('two_fa_code');

        if (!$g->checkCode($secret, $code)) {
            $w->error("incorrect info", "/auth/login");
        }

        $user->updateLastLogin();

        if (!$skip_session) {
            $w->session('user_id', $user->id);
            $w->session('timezone', "Australia/Sydney");
        }

        $w->sessionUnset('2fa');
        $w->sessionUnset('2fa_user_id');
    }

    else if ($w->session('2fa') == 0) {
        if ($w->session('orig_path') == "auth/login") {
            $w->redirect(!empty($w->session("user_redirect_url")) ? $w->localUrl($w->session("user_redirect_url")) : $w->localUrl());
        }
    
        $url = $w->session('orig_path');
        $w->Log->debug("Original path: " . $url);
    
        // If no url specified, go to the users defined url
        if (empty($url) || $url == "/") {
            $url = $w->session("user_redirect_url");
        }

        $w->sessionUnset('user_redirect_url');
    
        $w->sessionUnset('orig_path');
        $w->redirect($w->localUrl($url));
    }
}
