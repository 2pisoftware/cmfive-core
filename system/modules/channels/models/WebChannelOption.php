<?php

class WebChannelOption extends DbObject {

    static $_db_table = "channel_web_option";
    public $_channeltype = "web";
    public $channel_id;
    public $url;

    //public function __construct(Web $w) {
    //    parent::__construct($w);
    //}

    public function delete($force = false) {
        $channel = $this->getChannel();
        $channel->delete($force);

        parent::delete($force);
    }

    public function getChannel() {
        if (!empty($this->channel_id)) {
            return $this->w->Channel->getChannel($this->channel_id);
        }
        return null;
    }

    public function getNotifyUser() {
        $channel = $this->getChannel();
        if (!empty($channel)) {
            return $channel->getNotifyUser();
        }
    }
    
    public function read() {

        $this->w->Log->info("Getting messages from " . $this->url);
        $result = url_get_contents($this->url);
        if (strlen($result) > 0) {
            $this->w->Log->info("Read " . strlen($result));

            // Create message
            $channel_message = new ChannelMessage($this->w);
            $channel_message->channel_id = $this->channel_id;
            $channel_message->message_type = $this->_channeltype;
            $channel_message->is_processed = 0;
            $channel_message->insert();

            $attachment_id = $this->w->File->saveFileContent($channel_message, $result, "rawweb.txt", "channel_web_raw", "text/plain");
        } else {
            $this->w->Log->info("No response");
        }
    }
}

function url_get_contents ($Url) {
    if (!function_exists('curl_init')){ 
        die('CURL is not installed!');
    }
    //echo "<br />Error?? ".curl_error($ch);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $Url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);   
    $output = curl_exec($ch);
    //echo "<br />Error?? ".curl_error($ch);
    //echo "<br />Output ".($output);
    curl_close($ch);
    return $output;
}