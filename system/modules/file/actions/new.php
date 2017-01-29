<?php

function new_GET(Web $w) {
	
	$redirect_url = $w->request("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));
	
	$p = $w->pathMatch("class", "class_id");
	if (empty($p['class']) || empty($p['class_id'])) {
		$w->error("Missing class parameters", $redirect_url);
	}
	
	$_form = [
		'New Attachment' => [
			[(new \Html\Form\InputField\File())->setName("file")->setId("file")->setAttribute("capture", "camera")], // ["File", "file", "file"]
			[["Title", "text", "title"]],
			[["Description", "textarea", "description", "",null,null,'justtext']]
		]
	];

	$w->ctx("form", Html::multiColForm($_form, "/file/new/" . $p['class'] . '/' . $p['class_id'] . "?redirect_url=" . $redirect_url, "POST", "Save", 'file_form'));
}

function new_POST(Web $w) {
	$redirect_url = $w->request("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));
	
	$p = $w->pathMatch("class", "class_id");
	if (empty($p['class']) || empty($p['class_id'])) {
		$w->error("Missing class parameters", $redirect_url);
	}
	
	$object = $w->File->getObject($p['class'], $p['class_id']);
	
	if (empty($object->id)) {
		$w->error("Object not found", $redirect_url);
	}
	
	$result = $w->File->uploadAttachment("file", $object, $_POST['title'], $_POST['description'], !empty($_POST['type_code']) ? $_POST['type_code'] : null);
    if (empty($result)) {
        $w->error("No file found for attachment", $redirect_url);
    } elseif(!empty($_POST['file'])) {
		$w->out(json_encode(array('success'=> 'true', 'key' => $_POST['key'])));
	} else {
		$w->msg("File attached", $redirect_url);
	}
}
