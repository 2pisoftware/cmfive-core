<?php
function save_ALL(Web &$w) {
	$w->setLayout(null);
	$p = $w->pathMatch("classname");
	$token = $w->request("token");
	$record=[];
	foreach ($_REQUEST as $k => $v) {
		$record[urldecode($k)]=urldecode($v);
	}
	if (!array_key_exists('id',$_REQUEST)) {
		$record['id']=0;
	}
	$w->out($w->Rest->saveJson($p['classname'],$record['id'],$record, $token));
	
}