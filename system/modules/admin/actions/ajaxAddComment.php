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
	} else {
		$current_viewers = $comment->getViewerLinks();
		foreach (empty($current_viewers) ? [] : $current_viewers as $current_viewer) {
			$current_viewer->delete();
		}
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

	$notify_recipient_ids = [];
	foreach ($request_data->viewers as $viewer) {
		if ($viewer->is_notify && $viewer->can_view) {
			$notify_recipient_ids[] = $viewer->id;
		}
	}

	$notify_results = $w->callHook("comment", "send_notification_recipients_" . $top_object_table_name, [
		"object_id" => $top_object_id,
		"recipients" => $notify_recipient_ids,
		"commentor_id" => $user->id,
		"comment" => $request_data->is_restricted ? "This comment contains restricted inforation, please click to veiew it within Cmfive" : $comment->comment,
		"is_new" => $is_new
	]);

	if ($request_data->is_restricted) {
		$comment->setOwner($user->id);

		foreach (!empty($request_data->viewers) ? $request_data->viewers : [] as $viewer) {
			if ($viewer->can_view) {
				$comment->addViewer($viewer->id);
			}
		}
	}

	$w->out((new AxiosResponse())->setSuccessfulResponse("OK", []));
}