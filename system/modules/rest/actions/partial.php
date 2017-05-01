<?php
function partial_GET(Web &$w) {
	//$w->setLayout(null);
	// LIMIT 40 TOKENS IN SEARCH QUERY
	$p=$w->pathMatch('module','partial','classname',"token1","token2","token3","token4","token5","token6","token7","token8","token9","token10","token11","token12","token13","token14","token15","token16","token17","token18","token19","token20","token21","token22","token23","token24","token25","token26","token27","token28","token29","token30","token31","token32","token33","token34","token35","token36","token37","token38","token39","token40");
	$token = $w->request("token");
	$count=0;
	foreach($p as $pk=>$pv) {
		if ($count>2) $where[]=$pv;
		$count++;
	} 
	$results=[];
	if (count($where)>0) {
		// temporarily remove skip and limit from start and inject is deleted=0 before putting them back at the start again
		$skip=[];
		$limit=[];
		if ($where[0]=="SKIP") {
			$skip=array_slice($where,0,2);
			$where=array_slice($where,2);
		}
		if ($where[0]=="LIMIT") {
			$limit=array_slice($where,0,2);
			$where=array_slice($where,2);
		}
		$where=array_merge(array('is_deleted___equal','0'),$where);
		if (is_array($skip) && count($skip)>0) $where=array_merge($skip,$where);
		if (is_array($limit) && count($limit)>0) $where=array_merge($limit,$where);
		$results=json_decode($w->Rest->listJson($p['classname'],$where,$token));
	}
	//print_r($where);
	$w->ctx('results',$results);
	$w->ctx('module',$p['module']);
	$w->ctx('partial',$p['partial']);
	$w->ctx('classname',$p['classname']);	
}
