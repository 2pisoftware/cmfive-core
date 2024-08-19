<?php

function login_GET(Web $w)
{
    // CmfiveScriptComponentRegister::requireVue3();

    // Check if logged in already
    $user = AuthService::getInstance($w)->user();
    if (AuthService::getInstance($w)->loggedIn() && AuthService::getInstance($w)->allowed($user->redirect_url)) {
        $w->redirect($w->localUrl(!empty($user->redirect_url) ? $user->redirect_url : "/main"));
    }

    $loginform = Html::form([
        ["Application Login", "section"],
        ["Username", "text", "login"],
        ["Password", "password", "password"],
    ], $w->localUrl("auth/login"), "POST", "Login");

    $w->ctx("loginform", $loginform);
    $w->ctx("passwordHelp", Config::get('auth.access_hint', 'Forgot Password?'));
}

function login_POST(Web $w)
{
    $w->setLayout(null);

    $request_data = json_decode(file_get_contents("php://input"), true);
    if (empty($request_data) || !array_key_exists("login", $request_data) || !array_key_exists("password", $request_data)) {
        $w->out((new JsonResponse())->setErrorResponse("Please enter your login and password", "Please enter your login and password"));
    }

    $login = $request_data["login"];
    $password = $request_data["password"];
    $mfa_code = array_key_exists("mfa_code", $request_data) ? $request_data["mfa_code"] : null;

    if (empty($login) || empty($password)) {
        $w->out((new JsonResponse())->setErrorResponse("Please enter your login and password", "Please enter your login and password"));
    }

    $user = AuthService::getInstance($w)->getUserForLogin($login);
    if (empty($user)) {
        $w->out((new JsonResponse())->setErrorResponse("Incorrect login details", "Incorrect login details"));
        return;
    }

    if (Config::get('auth.login.attempts.track_attempts', false) == true) {
        if ($user->is_locked == 1) {
            $w->out((new JsonResponse())->setErrorResponse("This account is locked, most likely due to too many login attempts. Please contact an Administrator to get your account unlocked", "This account is locked, most likely due to too many login attempts. Please contact an Administrator to get your account unlocked"));
            return;
        }
    }

    if ($user->is_mfa_enabled && empty($mfa_code)) {
        $w->out((new JsonResponse())->setSuccessfulResponse(null, ["is_mfa_enabled" => true]));
        return;
    }

    $user = AuthService::getInstance($w)->login($login, $password, "Australia/Sydney", false, $mfa_code);
    if (empty($user)) {
        if (Config::get('auth.login.attempts.track_attempts', false) === true) {
            AuthService::getInstance($w)->recordLoginAttempt($login);
        }
        $w->out((new JsonResponse())->setErrorResponse("Incorrect login details", "Incorrect login details"));
        return;
    }

    $user->resetAttempts();

    if ($w->session('orig_path') != "auth/login") {
        $url = $w->session('orig_path');
        LogService::getInstance($w)->debug("Original path: " . $url);

        // If no url specified, go to the users defined url
        if (empty($url) || $url == "/") {
            $url = $user->redirect_url;
        }
        $w->sessionUnset('orig_path');
        $w->out((new JsonResponse())->setSuccessfulResponse(null, ["redirect_url" => $w->localUrl($url)]));
    } else {
        $w->out((new JsonResponse())->setSuccessfulResponse(null, ["redirect_url" => !empty($user->redirect_url) ? $w->localUrl($user->redirect_url) : $w->localUrl()]));
    }
}
