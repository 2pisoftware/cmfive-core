<?php

use Carbon\CarbonInterval;

function forgotpassword_GET(Web $w)
{
    // Check if logged in already
    $user = AuthService::getInstance($w)->user();
    if (AuthService::getInstance($w)->loggedIn() && AuthService::getInstance($w)->allowed($user->redirect_url)) {
        $w->redirect($w->localUrl(!empty($user->redirect_url) ? $user->redirect_url : "/main"));
    }

    $w->ctx("pagetitle", "Forgot Password");
}

function forgotpassword_POST(Web $w)
{
    $support_email = Config::get('main.company_support_email');
    if (empty($support_email)) {
        LogService::getInstance($w)->error("Cannot send recovery email. This site has not been configured with a default email address. Th project config needs a main.company_support_email record.");
        $w->error("Cannot send recovery email. This site has not been configured with a default email address", "/auth/login");
    }

    $user = AuthService::getInstance($w)->getUserForLogin(Request::string('login'));
    $responseString = "If this account exists then a password reset email has been just sent to the associated email address.";

    // For someone trying to gain access to a system, this is one of the
    // easiest ways to find a valid login, using the security through obscurity
    // principle, we dont tell them if it was a valid user or not, and we can log if they get it wrong
    // Note the previous message was "Could not find your account"
    if (!$user) {
        $w->msg($responseString, "/auth/login");
    }
    $user_contact = $user->getContact();

    // Generate password reset token
    $user->password_reset_token = bin2hex(random_bytes(16));
    $user->dt_password_reset_at = time();
    $user->update();

    // default 30 minutes
    $expiry = Config::get("auth.login.password.reset_token_expiry", 30 * 60);
    $readable_expiry = CarbonInterval::seconds($expiry)->cascade()->forHumans();

    // Send email
    $message = "Hello {$user->getFullName()},\n<br/>";
    $message .= "Please go to this link to reset your password:<br/>\n";
    $message .= "<a href=\"https://" . $_SERVER["HTTP_HOST"] . "/auth/resetpassword?token={$user->password_reset_token}\">https://"
        . $_SERVER["HTTP_HOST"] . "/auth/resetpassword?token={$user->password_reset_token}</a>\n<br/>You have {$readable_expiry} to reset your password.<br/><br/>";
    $message .= "Thank you,\n<br/>". Config::get('main.company_name', 'Cosine');

    $result = MailService::getInstance($w)->sendMail($user_contact->email, $support_email, Config::get("main.application_name") . " password reset", $message);
    if ($result !== 0) {
        $w->msg($responseString, "/auth/login");
    } else {
        $w->error("There was a problem sending an email, check your settings.", "/auth/login");
    }
}
