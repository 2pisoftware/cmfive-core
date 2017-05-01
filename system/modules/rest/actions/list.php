<?php
function list_GET(Web &$w) {
	$w->setLayout(null);
	$p = $w->pathMatch("classname","where_key","where_val");
	$token = $w->request("token");
	
	if ($p['where_key'] && $p['where_val']) {
		$where = array($p['where_key'] => $p['where_val']);		
	} else {
		$where = null;
	}
	$w->out($w->Rest->listJson($p['classname'],$where,$token));
}