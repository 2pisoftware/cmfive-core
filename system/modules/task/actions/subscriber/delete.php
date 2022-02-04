<?php

function delete_GET(Web $w) {

	list($subscriber_id) = $w->pathMatch();
	$subscriber = TaskService::getInstance($w)->getSubscriber($subscriber_id);

	if (!empty($subscriber->id)) {
		$subscriber->delete();
		$w->msg("Subscriber removed", '/task/edit/' . $subscriber->task_id);
	} else {
		$w->error("Subscriber not found", '/task');
	}

}