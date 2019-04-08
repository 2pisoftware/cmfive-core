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

	$is_new = false;

	if (empty($comment)) {
		$comment = new Comment($w);
		$comment->is_internal = $request_data->is_internal_only;
		$is_new = true;
	}

	$top_object_table_name = $request_data->top_object_table_name;
	$top_object_id = $request_data->top_object_id;

	$comment->obj_table = $top_object_table_name;
	$comment->obj_id = $request_data->top_object_id;
	$comment->comment = strip_tags($request_data->comment);
	$comment->insertOrUpdate();


	if ($top_object_table_name === "comment") {
		$top_object = $w->Comment->getComment($top_object_id)->getParentObject();
		$top_object_table_name = $top_object->getDbTableName();
		$top_object_id = $top_object->id;
	}

	if (!empty($request_data->notify_recipients)) {
		$recipient_ids = [];

		foreach ($request_data->notify_recipients as $notify_recipient_id => $notify_recipient) {
			$notify_recipient_ids[] = $notify_recipient_id;
		}

		$notify_results = $w->callHook("comment", "send_notification_recipients_" . $top_object_table_name, [
			"object_id" => $top_object_id,
			"recipients" => $notify_recipient_ids,
			"commentor_id" => $user->id,
			"comment" => $comment,
			"is_new" => $is_new
		]);
	}

	if ($request_data->is_restricted) {
		$comment->setOwner($user->id);

		foreach (!empty($request_data->viewers) ? $request_data->viewers : [] as $viewer) {
			$comment->addViewer($viewer->id);
		}
	}

	$w->out((new AxiosResponse())->setSuccessfulResponse("OK", []));
}