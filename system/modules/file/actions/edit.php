<?php

function edit_GET(Web $w) {
	
	// $p = $w->pathMatch("id");
	// $redirect_url = $w->request("redirect_url");
	// $redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));
	
	// if (empty($p['id'])) {
	// 	$w->error("Missing attachment ID", $redirect_url);
	// }
	
	// $attachment = $w->File->getAttachment($p['id']);
	// if (empty($attachment->id)) {
	// 	$w->error("Attachment not found", $redirect_url);
	// }
	
	// $_form = [
	// 	'Edit Attachment' => [
    //         [(new \Html\Form\InputField\File())->setName("file")->setId("file")->setAttribute("capture", "camera")],
	// 		[["Title", "text", "title", $attachment->title]],
	// 		[["Description", "textarea", "description", $attachment->description,null,null,'justtext']]
	// 	]
	// ];
	
	// $w->ctx('form', Html::multiColForm($_form, "/file/edit/" . $attachment->id . "?redirect_url=" . $redirect_url, 'POST', 'Save', 'file_form'));

	$redirect_url = $w->request("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));
	
	list($attachment_id) = $w->pathMatch("id");
	if (empty($attachment_id)) {
		$w->error("Missing attachment ID", $redirect_url);
	}

	$attachment = $w->File->getAttachment($attachment_id);
	if (empty($attachment)) {
		$w->error("Attachment not found", $redirect_url);
	}

	$owner = $attachment->getOwner();

	$viewers = $w->db->get("user")
		->select()
		->select("user.id, contact.firstname, contact.lastname, restricted_object_user_link.id 'link_id'")
		->leftJoin("contact ON contact.id = user.contact_id")
		->leftJoin("restricted_object_user_link ON restricted_object_user_link.user_id = user.id AND restricted_object_user_link.is_deleted = 0 AND restricted_object_user_link.type = 'viewer'")
		->where("user.is_deleted", 0)
		->where("user.id != ?", $w->Auth->user()->id)
		->fetchAll();

	$w->ctx("title", $attachment->title);
	$w->ctx("description", $attachment->description);
	$w->ctx("file_name", $attachment->filename);
	$w->ctx("file_directory", WEBROOT . "/file/atfile/" . $attachment->id . "/" . $attachment->filename);
	$w->ctx("redirect_url", WEBROOT . "/" . $redirect_url);
	$w->ctx("is_restricted", empty($owner) ? false : true);
	$w->ctx("viewers", json_encode($viewers));
	$w->ctx("can_restrict", $w->Auth->user()->hasRole("restrict") ? "true" : "false");
}

function edit_POST(Web $w) {
	
	$p = $w->pathMatch("id");
	$redirect_url = $w->request("redirect_url");
	$redirect_url = defaultVal($redirect_url, defaultVal($_SERVER["REQUEST_URI"], "/"));
	
	if (empty($p['id'])) {
		$w->error("Missing attachment ID", $redirect_url);
	}
	
	$attachment = $w->File->getAttachment($p['id']);
	if (empty($attachment->id)) {
		$w->error("Attachment not found", $redirect_url);
	}
        
        
        $attachment->updateAttachment("file");        
	$attachment->title = $_POST['title'];
	$attachment->description = $_POST['description'];
	$attachment->update();
	
	$w->msg("Attachment updated", $redirect_url);
	
}
