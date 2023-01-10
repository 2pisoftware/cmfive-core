<?php

function auth_admin_remove_user(Web $w, User $user) {
    return $w->partial("removeUser", ["user" => $user, "redirect" => "/admin-user/remove/" . $user->id], "auth");
}

/**
 * This method will be called on every action. If auth.require_mfa is turned on and the user doesn't have MFA
 * We will see a banner
 *
 * @param Web $w
 * @return void
 */
function auth_core_web_after(Web $w)
{
    $user = AuthService::getInstance($w)->user();
    if (empty($user)) {
        return;
    }
    if (Config::get('auth.require_mfa', false) == true && !$user->is_mfa_enabled) {
        $w->ctx('error', Config::get('auth.mfa_message', 'Two Factor Authentication (2FA) is required to use ' . Config::get('main.application_name', 'this system') . '. Please go to the Security tab in <a class="text-info" href="/auth/profile">your profile</a> to set 2FA up.'));
    }
}