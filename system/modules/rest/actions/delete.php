<?php

function delete_POST(Web &$w) {
	$w->setLayout(null);
	$p = $w->pathMatch("classname","id");
	$token = $w->request("token");
	
	$w->out($w->Rest->deleteJson($p['classname'],$p['id'], $token));	

}
