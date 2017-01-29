<?php

function login_GET(Web $w) {
    // Check if logged in already
    $user = $w->Auth->user();
    if ($w->Auth->loggedIn() && $w->Auth->allowed($user->redirect_url)) {
        $w->redirect($w->localUrl(!empty($user->redirect_url) ? $user->redirect_url : "/main"));
    }
        $loginform = Html::form(array(
                    array("Application Login", "section"),
                    array("Username", "text", "login"),
                    array("Password", "password", "password"),
                        ), $w->localUrl("auth/login"), "POST", "Login");
    $w->ctx("loginform", $loginform);
}

function login_POST(Web &$w) {
    if ($_POST['login'] && $_POST['password']) {
        $client_timezone = "Australia/Sydney"; //$_POST['user_timezone'];
        $user = $w->Auth->login($_POST['login'], $_POST['password'], $client_timezone);
        if ($user) {
            if ($w->session('orig_path') != "auth/login") {
                $url = $w->session('orig_path');
                $w->Log->debug("Original path: " . $url);

                // If no url specified, go to the users defined url
                if (empty($url) || $url == "/") {
                    $url = $user->redirect_url;
                }
                $w->sessionUnset('orig_path');
                $w->redirect($w->localUrl($url));
            } else {
                $w->redirect(!empty($user->redirect_url) ? $w->localUrl($user->redirect_url) : $w->localUrl());
            }
        } else {
            $w->error("Login or Password incorrect", "/auth/login");
        }
    } else {
        $w->error("Please enter your login and password", "/auth/login");
    }
}
