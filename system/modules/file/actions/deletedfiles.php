<?php

function deletedfiles_GET(Web $w) {
	
	$w->ctx("deleted_files", FileService::getInstance($w)->getObjects("Attachment", ["is_deleted" => 1]));
	
}
