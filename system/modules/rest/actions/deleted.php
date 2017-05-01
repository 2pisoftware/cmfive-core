<?php
function deleted_GET(Web &$w) {
	$w->setLayout(null);
	$p = $w->pathMatch("classname","where_key1","where_val1","where_key2","where_val2","where_key3","where_val3","where_key4","where_val4","where_key5","where_val5");
	$token = $w->request("token");
	$found=false;
	$where = array();
	for ($i=1; $i<6; $i++) {
		if ($p['where_key'+$i] && $p['where_val'+$i]) {
			$where[$p['where_key'+$i]]=$p['where_val'+$i];
		}
	}
	$w->out($w->Rest->listJson($p['classname'],$where,$token));
}
