<?php
function attach_GET(Web &$w) {
	$w->setLayout(null);
	$p = $w->pathMatch("table","id","url");
	$object = $w->Auth->getObject($p['table'],$p['id']);
	if (!$object) {
		$w->error(__("Nothing to attach to."));
	}
	$types = $w->File->getAttachmentTypesForObject($object);
	$w->ctx("types",$types);
        $w->ctx("table", $p['table']);
        $w->ctx("id", $p['id']);
        $w->ctx("url", $p['url']);
}

function attach_POST(Web &$w) {
	$table = $w->request('table');
	$id = $w->request('id');
	$title = $w->request('title');
	$description = $w->request('description');
	$type_code = $w->request('type_code');

	$url = str_replace(" ", "/", $w->request('url'));
	$object = $w->Auth->getObject($table,$id);
	if (!$object) {
		$w->error(__("Nothing to attach to."),$url);
	}

	$aid = $w->service("File")->uploadAttachment("file",$object,$title,$description,$type_code);
	if ($aid) {
		$w->ctx('attach_id',$aid);
		$w->ctx('attach_table',$table);
		$w->ctx('attach_table_id',$id);
		$w->ctx('attach_title',$title);
		$w->ctx('attach_description',$description);
		$w->ctx('attach_type_code',$type_code);
		$w->msg(__("File attached."),$url);
	} else {
		$w->error(__("There was an error. Attachment could not be saved."),$url);
	}
}
