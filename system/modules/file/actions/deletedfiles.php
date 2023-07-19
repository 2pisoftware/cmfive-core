<?php

function deletedfiles_GET(Web $w) {

	$w->setLayout('layout-bootstrap-5');
	
	// Need to use $includeDeleted
	$w->ctx("deleted_files", FileService::getInstance($w)->getObjects("Attachment", ["is_deleted" => 1], false, true, null, null, null, true));
	
}
