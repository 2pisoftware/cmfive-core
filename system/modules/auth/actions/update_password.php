<?php

function update_password_GET(Web $w)
{

}

function update_password_POST(Web $w)
{
    if (empty($_POST["password"]) || empty($_POST["confirm_password"])) {
        $w->error("Missing Password or Confirm Password", "/auth/update_password");
    }

    if ($_POST["password"] !== $_POST["confirm_password"]) {
        $w->error("Password and Confirm Password do not match", "/auth/update_password");
    }

    $user = $w->Auth->user();
    if (empty($user)) {
        $w->error("Not logged in", "/auth/login");
    }

    // Remove the User's salt because it will be stored in the password field from now on.
    if (!empty($user->password_salt)) {
        $user->password_salt = null;
        $user->update(true);
    }

    // Update the User's password using password_hash.
    $user->setPassword($_POST["password"]);
    $user->is_password_invalid = false;
    $user->update();

    // Redirect the User.
    if ($w->session('orig_path') === "auth/login") {
        $w->redirect(!empty($user->redirect_url) ? $w->localUrl($user->redirect_url) : $w->localUrl());
    }

    $url = $w->session('orig_path');
    $w->Log->debug("Original path: " . $url);

    if (empty($url) || $url == "/") {
        $url = $user->redirect_url;
    }

    $w->sessionUnset('orig_path');
    $w->redirect($w->localUrl($url));
}
