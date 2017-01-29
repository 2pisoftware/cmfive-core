<?php

class ChannelProcessor extends DbObject {

    public $name;
    public $class;
    public $module;
    public $processor_settings;
    public $settings; // Was filter_settings, not sure why
    public $channel_id;

    public function getChannel() {
        return $this->w->Channel->getChannel($this->channel_id);
    }

    public function retrieveProcessor() {
        try {
            $processor = new $this->class($this->w);
            return $processor;
        } catch (Exception $e) {
            return null;
        }
    }

}
