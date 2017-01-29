<?php

function deletedfiles_GET(Web $w) {
	
	$w->ctx("deleted_files", $w->File->getObjects("Attachment", ["is_deleted" => 1]));
	
}
