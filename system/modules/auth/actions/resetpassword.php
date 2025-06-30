<?php

use Carbon\CarbonInterval;
use Html\Form\InputField\Password;

function resetpassword_GET(Web $w)
{
    $token = Request::string('token'); // token

    /** @var User $user */
    $user = AuthService::getInstance($w)->getUserForToken($token);
    $validData = false;

    if (!empty($user->id)) {
        // Check that the password reset hasn't expired
        LogService::getInstance($w)->setLogger("AUTH")->debug("USER: " . $user->id . " TIME: " . time() . " USER_RESET: " . $user->dt_password_reset_at . " RESULT: " . (time() - $user->dt_password_reset_at));
        
        // default 30 minutes
        $expiry = Config::get("auth.login.password.reset_token_expiry", 30 * 60);
        $readable_expiry = CarbonInterval::seconds($expiry)->cascade()->forHumans();

        if ((time() - $user->dt_password_reset_at) > $expiry) {
            $w->msg("Your token has expired (max {$readable_expiry}), please submit for a new one", "/auth/forgotpassword");
            return;
        }

        $password_field = (new Password([
            'id|name' => 'password',
            'label' => 'New password'
        ]));

        $password_confirm_field = (new Password([
            'id|name' => 'password_confirm',
            'label' => 'Confirm password'
        ]));

        if (Config::get('auth.login.password.enforce_length') === true) {
            $password_field->setMinlength(Config::get('auth.login.password.min_length', 8));
            $password_confirm_field->setMinlength(Config::get('auth.login.password.min_length', 8));
        }

        $user_contact = $user->getContact();
        if (!empty($user_contact)) {
            $password_form = HtmlBootstrap5::form([
                ["Enter new password", "section"],
                $password_field,
                $password_confirm_field,
            ], $w->localUrl("auth/resetpassword?token=$token"), "POST", "Reset");
            $w->out($password_form);
            $validData = true;
        }
    }

    if (!$validData) {
        LogService::getInstance($w)->warn("Password reset attempt failed with token: $token");
        $w->out("Invalid token, this incident has been logged");
    }
}

function resetpassword_POST(Web $w)
{
    $token = Request::string('token'); // token
    $password = Request::string('password'); // password
    $password_confirm = Request::string('password_confirm');

    if ($password !== $password_confirm) {
        $w->error("Passwords do not match", "/auth/resetpassword?token=$token");
        return;
    }

    if (Config::get('auth.login.password.enforce_length') === true) {
        if (strlen($password) < Config::get('auth.login.password.min_length', 8)) {
            $w->error('Password does not meet minimum length requirements', "/auth/resetpassword?token=$token");
        }
    }

    $user = AuthService::getInstance($w)->getUserForToken($token);
    $validData = false;

    if (!empty($user->id)) {
        // Check that the password reset hasn't expired
        if ((time() - $user->dt_password_reset_at) > 86400) {
            $w->msg("Your token has expired (max 24 hours), please submit for a new one", "/auth/forgotpassword");
            return;
        }

        $user_contact = $user->getContact();
        if (!empty($user_contact)) {
            $user->setPassword($password);
            $user->password_reset_token = null;
            $user->dt_password_reset_at = null;
            $user->update(true);

            // Precautionary logout
            if (AuthService::getInstance($w)->loggedIn()) {
                $w->sessionDestroy();
            }

            $validData = true;
        }
    }

    if (!$validData) {
        LogService::getInstance($w)->warn("Password reset attempt failed with token: $token");
        $w->out("Invalid token, this incident has been logged");
    } else {
        $w->msg("Your password has been reset", "/auth/login");
    }
}
