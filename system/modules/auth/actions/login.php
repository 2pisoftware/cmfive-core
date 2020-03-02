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
}

function login_POST(Web &$w)
{
    $w->setLayout(null);

    $request_data = json_decode(file_get_contents("php://input"), true);
    if (empty($request_data)) {
        $w->error("Please enter your login and password", "/auth/login");
    }

    $login = $request_data["login"];
    $password = $request_data["password"];
    $mfa_code = array_key_exists("mfa_code", $request_data) ? $request_data["mfa_code"] : null;

    if (empty($login) || empty($password)) {
        $w->error("Please enter your login and password", "/auth/login");
    }

    $user = $w->Auth->getUserForLogin($login);
    if (empty($user)) {
        $w->error("Please enter your login and password", "/auth/login");
    }

    if ($user->is_mfa_enabled && empty($mfa_code)) {
        $w->out((new AxiosResponse())->setSuccessfulResponse(null, ["is_mfa_enabled" => true]));
        return;
    }

    $user = $w->Auth->login($login, $password, "Australia/Sydney", false, $mfa_code);
    if (empty($user)) {
        $w->out((new AxiosResponse())->setErrorResponse("Incorrect login details", null));
        return;
    }

    if ($w->session('orig_path') != "auth/login") {
        $url = $w->session('orig_path');
        $w->Log->debug("Original path: " . $url);

        // If no url specified, go to the users defined url
        if (empty($url) || $url == "/") {
            $url = $user->redirect_url;
        }
        $w->sessionUnset('orig_path');
        $w->out((new AxiosResponse())->setSuccessfulResponse(null, ["redirect_url" => $w->localUrl($url)]));
    } else {
        $w->out((new AxiosResponse())->setSuccessfulResponse(null, ["redirect_url" => !empty($user->redirect_url) ? $w->localUrl($user->redirect_url) : $w->localUrl()]));
    }
}
