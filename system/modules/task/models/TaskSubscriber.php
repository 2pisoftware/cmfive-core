<?php

class TaskSubscriber extends DbObejct {

	public $task_id;
	public $user_id;

	public function getTask() {
		return $this->getObject("Task", $this->task_id);
	}

	public function getUser() {
		return $this->getObject("User", $this->user_id);
	}

}