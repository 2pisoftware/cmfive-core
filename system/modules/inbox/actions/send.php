<?php
function send_GET(Web $w) {
	$w->Inbox->navigation($w,__("Create Message"));
}
function send_POST(Web &$w) {
	$p = $w->pathMatch('id');
	if ($w->request("receiver_id")>0) { 
		if($p['id']){
			// For reply function
			$mess = $w->Inbox->getMessage($p['id']);
			$w->Inbox->addMessage($w->request("subject"),$w->request("message"),$w->request("receiver_id"),null,$p['id']);
			$mess->has_parent = 1;
			$mess->update();
		} else {
			// To generate test data cause im lazy
			$receiver_id = $w->request("receiver_id");
			$subject = $w->request("subject");
			$message = $w->request("message");
			if ($receiver_id && $subject) {
				$w->Inbox->addMessage($subject, $message, $receiver_id);
			}
		}
		$w->msg("Message Sent.","/inbox/index");
	} else {
		$w->error("You must enter a message recipient.","/inbox/send/".$p['id']);
	}
	$w->msg(__("Message Sent."),"/inbox/index");
}
