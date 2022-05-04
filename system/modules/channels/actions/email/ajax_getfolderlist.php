<?php

function ajax_getfolderlist_ALL(Web $w)
{
    $emailchannel = new EmailChannelOption($w);
    $emailchannel->server = urldecode(Request::string("server"));
    $emailchannel->s_username = urldecode(Request::string("s_username"));
    $emailchannel->s_password = urldecode(Request::string("s_password"));
    $emailchannel->use_auth = Request::string("use_auth");

    $folders = $emailchannel->getFolderList(false);

    $response = ["success" => false, "response" => ""];
    if (!empty($folders)) {
        if (is_array($folders)) {
            // echo json_encode($folders);
            $response["success"] = true;
            $response["response"] = $folders;
        } elseif (is_string($folders)) {
            $response["response"] = $folders;
        } else {
            $response["response"] = "Folders not found (maybe a misconfiguration?)";
        }
    }

    echo json_encode($response);
}
