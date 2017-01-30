<?php

function resetpassword_GET(Web $w) {
    $email = $w->request('email'); // email
    $token = $w->request('token'); // token

    $user = $w->Auth->getUserForToken($token); //this->getObject("User", array("password_reset_token", $token));
    $validData = false;
    if (!empty($user->id)) {
        // Check that the password reset hasn't expired
        $w->Log->setLogger("AUTH")->debug("USER: " . $user->id . " TIME: " . time() . " USER_RESET: " . $user->dt_password_reset_at . " RESULT: " . (time() - $user->dt_password_reset_at));
        if ((time() - $user->dt_password_reset_at) > 86400) {
            $w->msg(__("Your token has expired (max 24 hours), please submit for a new one"), "/auth/forgotpassword");
            return;
        }

        $user_contact = $user->getContact();
        if (!empty($user_contact)) {
            if ($user_contact->email == $email) {
                // We have passed the test
                $password_form = Html::form(array(
                            array(__("Enter new password"), "section"),
                            array(__("New password"), "password", "password"),
                            array(__("Confirm password"), "password", "password_confirm"),
                                ), $w->localUrl("auth/resetpassword?email=$email&token=$token"), "POST", __("Reset"));
                $w->out($password_form);
                $validData = true;
            }
        }
    }
    if (!$validData) {
        $w->Log->warn("Password reset attempt failed with email: $email, token: $token");
        $w->out(__("Invalid email or token, this incident has been logged"));
    }
}

function resetpassword_POST(Web $w) {
    $email = $w->request('email'); // email
    $token = $w->request('token'); // token
    $password = $w->request('password'); // password
    $password_confirm = $w->request('password_confirm');

    if ($password !== $password_confirm) {
        $w->error(__("Passwords do not match"), "/auth/resetpassword?email=$email&token=$token");
        return;
    }

    $user = $w->Auth->getUserForToken($token); //getObject("User", array("password_reset_token", $token));
    $validData = false;
    if (!empty($user->id)) {
        // Check that the password reset hasn't expired
        if ((time() - $user->dt_password_reset_at) > 86400) {
            $w->msg(__("Your token has expired (max 24 hours), please submit for a new one"), "/auth/forgotpassword");
            return;
        }

        $user_contact = $user->getContact();
        if (!empty($user_contact)) {
            if ($user_contact->email == $email) {
                $user->setPassword($password);
                $user->password_reset_token = null;
                $user->dt_password_reset_at = null;
                $user->update(true);

                // Precautionary logout
                if ($w->Auth->loggedIn()) {
                    $w->sessionDestroy();
                }

                $validData = true;
            }
        }
    }
    if (!$validData) {
        $w->Log->warn("Password reset attempt failed with email: $email, token: $token");
        $w->out(__("Invalid email or token, this incident has been logged"));
    } else {
        $w->msg(__("Your password has been reset"), "/auth/login");
    }
}
