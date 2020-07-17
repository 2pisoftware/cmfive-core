<?php
function logout_GET(Web &$w)
{
    if (AuthService::getInstance($w)->loggedIn()) {
        // Unset all of the session variables.
        $w->sessionDestroy();
    }
    $w->redirect($w->localUrl("/auth/login"));
}
