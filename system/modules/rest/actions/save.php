<?php
function save_POST(Web &$w) {
	$w->setLayout(null);
	$p = $w->pathMatch("classname");
	$token = $w->request("token");
	$record=[];
	foreach ($_REQUEST as $k => $v) {
		$record[urldecode($k)]=urldecode($v);
	}
	$id=null;
	if (array_key_exists('id',$record)) $id=$record['id'];
	$w->out($w->Rest->saveJson($p['classname'],$id,$record, $token));	

}
