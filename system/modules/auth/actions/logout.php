<?php
function logout_GET(Web &$w) {
	if ($w->Auth->loggedIn()) {
		// Unset all of the session variables.
		$w->sessionDestroy();
	}
	$w->redirect($w->localUrl("/auth/login"));
}