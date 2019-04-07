<?php

function ajaxAddComment_POST(Web $w) {
	$w->setLayout(null);

	$user = $w->Auth->user();
	if (!$user->hasRole("restrict")) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "User not authorised to restrict objects"]));
		return;
	}

	$request_data = json_decode(file_get_contents("php://input"));
	if (empty($request_data)) {
		$w->out((new AxiosResponse())->setErrorResponse(null, ["error_message" => "Missing comment data"]));
		return;
	}

	$comment = null;
	if (!empty($request_data->comment_id)) {
		$comment = $w->Comment->getComment($request_data->comment_id);
	}

	if (empty($comment)) {
		$comment = new Comment($w);
		$comment->is_internal = $request_data->internal_only;
	}

	$comment->obj_table = $request_data->table_name;
	$comment->obj_id = $request_data->object_id;
	$comment->comment = strip_tags($request_data->comment);
	$comment->insertOrUpdate();

	$top_table_name = $request_data->table_name;
	$top_id = $request_data->object_id;

	if ($top_table_name === "comment") {
		$top_object = $w->Comment->getComment($top_id)->getParentObject();
		$top_table_name = $top_object->getDbTableName();
		$top_id = $top_object->id;
	}

	if ($request_data->is_notifications) {
		$recipients = [];

	}
}