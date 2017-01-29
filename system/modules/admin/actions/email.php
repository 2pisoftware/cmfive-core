<?php

function email_GET(Web $w) {
	if (!class_exists("Mandrill")) {
		$w->ctx("error", "You don't have the Mandrill library installed");
		return;
	}
	
	$api_key = Config::get('email.api.credentials.key');
	if (empty($api_key)) {
		$w->ctx("error", "You don't have a Mandrill API key specified");
		return;
	}

	try {
		$w->ctx("title", "Email stats");
		// Instantiate Mandrill
		$mandrill = new Mandrill(Config::get('email.api.credentials.key'));

		$ping = $mandrill->users->ping();		// Ping should equal "PONG"
		$info = $mandrill->users->info();
		
		// Set searchable date range
		$date_from = new DateTime();
		$date_from->modify('-1 day');
		$date_to = new DateTime();
		
		$messages = $mandrill->messages->search('*', $date_from->format('Y-m-d'), $date_to->format('Y-m-d'));
		
		$w->ctx("info", $info);
		$w->ctx("messages", $messages);
	} catch (Mandrill_Error $e) {
		$w->ctx("error", "A Mandrill error occurred: " . get_class($e) . ' - ' . $e->getMessage());
	}
}