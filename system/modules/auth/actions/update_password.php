<?php

function update_password_GET(Web $w)
{
}

function update_password_POST(Web $w)
{
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($password) || empty($confirm_password)) {
        $w->error("Missing Password or Confirm Password", "/auth/update_password");
    }

    if ($password !== $confirm_password) {
        $w->error("Password and Confirm Password do not match", "/auth/update_password");
    }

    $user = AuthService::getInstance($w)->user();
    if (empty($user)) {
        $w->error("Not logged in", "/auth/login");
    }

    // Check if the User's password hash is depricated and update if so.
    if ($user->updatePasswordHash($password)) {
        LogService::getInstance($w)->info("User with ID: " . $user->id . " password hash was updated");
    }

    // Set the User's password to be valid again.
    $user->is_password_invalid = false;
    $user->update();

    // Redirect the User.
    if ($w->session('orig_path') === "auth/login") {
        $w->redirect(!empty($user->redirect_url) ? $w->localUrl($user->redirect_url) : $w->localUrl());
    }

    $url = $w->session('orig_path');
    LogService::getInstance($w)->debug("Original path: " . $url);

    if (empty($url) || $url == "/") {
        $url = $user->redirect_url;
    }

    $w->sessionUnset('orig_path');
    $w->redirect($w->localUrl($url));
}
