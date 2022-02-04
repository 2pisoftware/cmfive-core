<?php

function rollbackbatch_GET(Web $w) {
	$w->setLayout(null);
	
	$response = MigrationService::getInstance($w)->batchRollback();
	
	$w->msg($response, "/admin-migration");
	
}