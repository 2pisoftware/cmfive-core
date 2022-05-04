<?php
function attach_GET(Web &$w)
{
	$w->setLayout(null);
	$p = $w->pathMatch("table", "id", "url");
	$object = AuthService::getInstance($w)->getObject($p['table'], $p['id']);
	if (!$object) {
		$w->error("Nothing to attach to.");
	}
	$types = FileService::getInstance($w)->getAttachmentTypesForObject($object);
	$w->ctx("types", $types);
	$w->ctx("table", $p['table']);
	$w->ctx("id", $p['id']);
	$w->ctx("url", $p['url']);
}

function attach_POST(Web &$w)
{
	$table = Request::string('table');
	$id = Request::int('id');
	$title = Request::string('title');
	$description = Request::string('description');
	$type_code = Request::string('type_code');

	$url = str_replace(" ", "/", Request::string('url'));
	$object = AuthService::getInstance($w)->getObject($table, $id);
	if (!$object) {
		$w->error("Nothing to attach to.", $url);
	}

	$aid = FileService::getInstance($w)->uploadAttachment("file", $object, $title, $description, $type_code);
	if ($aid) {
		$w->ctx('attach_id', $aid);
		$w->ctx('attach_table', $table);
		$w->ctx('attach_table_id', $id);
		$w->ctx('attach_title', $title);
		$w->ctx('attach_description', $description);
		$w->ctx('attach_type_code', $type_code);
		$w->msg("File attached.", $url);
	} else {
		$w->error("There was an error. Attachment could not be saved.", $url);
	}
}
