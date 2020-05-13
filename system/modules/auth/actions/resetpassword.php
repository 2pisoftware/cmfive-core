<?php

function resetpassword_GET(Web $w)
{
    $token = $w->request('token'); // token

    $user = AuthService::getInstance($w)->getUserForToken($token);
    $validData = false;

    if (!empty($user->id)) {
        // Check that the password reset hasn't expired
        $w->Log->setLogger("AUTH")->debug("USER: " . $user->id . " TIME: " . time() . " USER_RESET: " . $user->dt_password_reset_at . " RESULT: " . (time() - $user->dt_password_reset_at));
        if ((time() - $user->dt_password_reset_at) > 86400) {
            $w->msg("Your token has expired (max 24 hours), please submit for a new one", "/auth/forgotpassword");
            return;
        }

        $user_contact = $user->getContact();
        if (!empty($user_contact)) {
            $password_form = Html::form([
                ["Enter new password", "section"],
                ["New password", "password", "password"],
                ["Confirm password", "password", "password_confirm"],
            ], $w->localUrl("auth/resetpassword?token=$token"), "POST", "Reset");
            $w->out($password_form);
            $validData = true;
        }
    }

    if (!$validData) {
        $w->Log->warn("Password reset attempt failed with token: $token");
        $w->out("Invalid token, this incident has been logged");
    }
}

function resetpassword_POST(Web $w)
{
    $token = $w->request('token'); // token
    $password = $w->request('password'); // password
    $password_confirm = $w->request('password_confirm');

    if ($password !== $password_confirm) {
        $w->error("Passwords do not match", "/auth/resetpassword?token=$token");
        return;
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
        $w->Log->warn("Password reset attempt failed with token: $token");
        $w->out("Invalid token, this incident has been logged");
    } else {
        $w->msg("Your password has been reset", "/auth/login");
    }
}
