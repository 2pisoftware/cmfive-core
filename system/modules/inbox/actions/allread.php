<?php
function allread_GET(Web &$w) {
	InboxService::getInstance($w)->markAllMessagesRead();
	$w->msg("All messages marked as read.","/inbox/index");
}