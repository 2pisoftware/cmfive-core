<?php

function login_GET(Web $w)
{
    // Check if logged in already
    $user = $w->Auth->user();
    if ($w->Auth->loggedIn() && $w->Auth->allowed($user->redirect_url)) {
        $w->redirect($w->localUrl(!empty($user->redirect_url) ? $user->redirect_url : "/main"));
    }

    $loginform = Html::form([
        ["Application Login", "section"],
        ["Username", "text", "login"],
        ["Password", "password", "password"],
    ], $w->localUrl("auth/login"), "POST", "Login");

    $w->ctx("loginform", $loginform);
    $w->ctx("passwordHelp", Config::get('auth.access_hint', 'Forgot Password?'));

    $w->enqueueScript(["name" => "vue.js", "uri" => "/system/templates/js/vue.js", "weight" => 2000]);
    CmfiveScriptComponentRegister::registerComponent("AxiosJS", new CmfiveScriptComponent("/system/templates/js/axios.min.js"));
    CmfiveScriptComponentRegister::registerComponent("ToastJS", new CmfiveScriptComponent("/system/templates/js/Toast.js"));
    CmfiveStyleComponentRegister::registerComponent("ToastSCSS", new CmfiveStyleComponent("/system/templates/css/Toast.scss", ["/system/templates/scss/"]));
}

function login_POST(Web &$w)
{
    if (!$_POST['login'] || $_POST['password']) {
        $w->error("Please enter your login and password", "/auth/login");
    }

    $user = $w->Auth->login($_POST['login'], $_POST['password'], "Australia/Sydney");
    if (!$user) {
        $w->error("Login or Password incorrect", "/auth/login");
    }

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
}
