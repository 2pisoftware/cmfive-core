<?php

class TaskService extends DbService {

    public function getTaskGroups() {
        return $this->getObjects('TaskGroup', ['is_active' => 1, 'is_deleted' => 0]);
    }

	public function getTaskGroup($id) {
        return $this->getObject('TaskGroup', $id);
    }

	public function getTasks() {
		return $this->getObjects('Task', ['is_deleted' => 0]);
	}

	public function getTask($id) {
		return $this->getObject('Task', $id);
	}

}