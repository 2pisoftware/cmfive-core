<?php
function token_GET(Web &$w) {
	$w->setLayout(null);
	$username = $w->request("username");
	$password = $w->request("password");
	$api = $w->request("api");
	$w->out($w->Rest->getTokenJson($api,$username,$password));
}
