<?php

function edit_GET(Web $w) {
	
	$p = $w->pathMatch("id");
	$redirect_url = $w->request("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));
	
	if (empty($p['id'])) {
		$w->error(__("Missing attachment ID"), $redirect_url);
	}
	
	$attachment = $w->File->getAttachment($p['id']);
	if (empty($attachment->id)) {
		$w->error(__("Attachment not found"), $redirect_url);
	}
	
	$_form = [
		'Edit Attachment' => [
            [(new \Html\Form\InputField\File())->setName("file")->setId("file")->setAttribute("capture", "camera")],
			[["Title", "text", "title", $attachment->title]],
			[["Description", "textarea", "description", $attachment->description,null,null,'justtext']]
		]
	];
	
	$w->out(Html::multiColForm($_form, "/file/edit/" . $attachment->id . "?redirect_url=" . $redirect_url));
}

function edit_POST(Web $w) {
	
	$p = $w->pathMatch("id");
	$redirect_url = $w->request("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));
	
	if (empty($p['id'])) {
		$w->error(__("Missing attachment ID"), $redirect_url);
	}
	
	$attachment = $w->File->getAttachment($p['id']);
	if (empty($attachment->id)) {
		$w->error(__("Attachment not found"), $redirect_url);
	}
        
        
        $attachment->updateAttachment("file");        
	$attachment->title = $_POST['title'];
	$attachment->description = $_POST['description'];
	$attachment->update();
	
	$w->msg(__("Attachment updated"), $redirect_url);
	
}
