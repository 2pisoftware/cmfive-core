<?php
function get_GET(Web &$w) {
	$w->setLayout(null);
	$p = $w->pathMatch("classname","id");
	$token = $w->request("token");
	
	$w->out($w->Rest->getJson($p['classname'],$p['id'], $token));	
}
