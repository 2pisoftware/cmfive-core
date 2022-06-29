<?php
function send_GET(Web $w)
{
	InboxService::getInstance($w)->navigation($w, "Create Message");
}
function send_POST(Web &$w)
{
	$p = $w->pathMatch('id');
	if (Request::int("receiver_id") > 0) {
		if ($p['id']) {
			// For reply function
			$mess = InboxService::getInstance($w)->getMessage($p['id']);
			InboxService::getInstance($w)->addMessage(Request::string("subject"), Request::string("message"), Request::int("receiver_id"), null, $p['id']);
			$mess->has_parent = 1;
			$mess->update();
		} else {
			// To generate test data cause im lazy
			$receiver_id = Request::int("receiver_id");
			$subject = Request::string("subject");
			$message = Request::string("message");
			if ($receiver_id && $subject) {
				InboxService::getInstance($w)->addMessage($subject, $message, $receiver_id);
			}
		}
		$w->msg("Message Sent.", "/inbox/index");
	} else {
		$w->error("You must enter a message recipient.", "/inbox/send/" . $p['id']);
	}
}
