<?php

function ajax_getfolderlist_ALL(Web $w) {
	$emailchannel = new EmailChannelOption($w);
	$emailchannel->server = urldecode($w->request("server"));
	$emailchannel->s_username = urldecode($w->request("s_username"));
	$emailchannel->s_password = urldecode($w->request("s_password"));
	$emailchannel->use_auth = $w->request("use_auth");

	$folders = $emailchannel->getFolderList(false);

	$response = array("success" => false, "response" => "");
	if (!empty($folders)) {
		if (is_array($folders)) {
			// echo json_encode($folders);
			$response["success"] = true;
			$response["response"] = $folders;
		} else if (is_string($folders)) {
			$response["response"] = $folders;
		} else {
			$response["response"] = "Folders not found (maybe a misconfiguration?)";
		}
	}

	echo json_encode($response);

}