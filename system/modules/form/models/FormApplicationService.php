<?php

class FormApplicationService extends DbService {

	public function getFormApplications() {
		return $this->getObjects("FormApplication", ['is_deleted' => 0]);
	}

	public function getFormApplication($id) {
		return $this->getObject('FormApplication', $id);
	}

}