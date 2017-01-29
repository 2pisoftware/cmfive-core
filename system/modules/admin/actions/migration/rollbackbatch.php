<?php

function rollbackbatch_GET(Web $w) {
	$w->setLayout(null);
	
	$response = $w->Migration->batchRollback();
	
	$w->msg($response, "/admin-migration");
	
}