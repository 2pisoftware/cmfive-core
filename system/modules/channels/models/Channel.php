<?php

class Channel extends DbObject {

    public $name;
    public $is_active; // 0|1 flag
    public $notify_user_email;
    public $notify_user_id;
    public $do_processing; // 0|1 flag

    public function getForm() {

        return array("Channel" => array(
                array(
                    array("Name", "text", "name", $this->name),
                    array("Is Active", "checkbox", "is_active", ($this->is_active === null ? 1 : $this->is_active))
                ),
                array(
                    array("Notify Email", "text", "notify_user_email", $this->notify_user_email),
                    // TODO: Need to prefil this with user names
                    array("Notify User", "select", "notify_user_id", $this->notify_user_id, $this->w->Auth->getUsers())
                ),
                array(
                    array("Run processors?", "checkbox", "do_processing", $this->do_processing)
                )
        ));
    }

    public function read() {
        $channelImpl = $this->Channel->getChildChannel($this->id);
        if (!empty($channelImpl)) {
            $channelImpl->read();
        }
        if ($this->do_processing) {
            // Clear anything in buffer before redirect
            $processors = $this->w->Channel->getProcessors($this->id);
            if (!empty($processors)) {
                foreach ($processors as $processor) {
                    $processor_class = $processor->retrieveProcessor();
                    $processor_class->process($processor);
                }
            }
        }
    }

    public function getNotifyUser() {
        return $this->Auth->getUser($this->notify_user_id);
    }

}
