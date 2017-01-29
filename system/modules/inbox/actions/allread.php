<?php
function allread_GET(Web &$w) {
	$w->Inbox->markAllMessagesRead();
	$w->msg("All messages marked as read.","/inbox/index");
}