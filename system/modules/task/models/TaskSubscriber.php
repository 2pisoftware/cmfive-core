<?php

/**
 * Database model for a task subscriber
 * A task subscriber is someone who will recieve any
 * notifications for interactions with a Task.
 *
 * @author Adam Buckley
 */
class TaskSubscriber extends DbObject {

	public $task_id;
	public $user_id;

	public function getTask() {
		return $this->getObject("Task", $this->task_id);
	}

	public function getUser() {
		return $this->getObject("User", $this->user_id);
	}

}