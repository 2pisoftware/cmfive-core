<?php

function auth_admin_remove_user(Web $w, User $user) {
	return $w->partial("removeUser", ["user" => $user, "redirect" => "/admin-user/remove/" . $user->id], "auth");
}