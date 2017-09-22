<?php

class FormApplicationService extends DbService {

	public function getFormApplications() {
		return $this->getObjects("FormApplication", ['is_deleted' => 0]);
	}

	public function getFormApplication($id) {
		return $this->getObject('FormApplication', $id);
	}

	public function getFormApplicationMember($application_id, $member_user_id) {
		return $this->getObject('FormApplicationMember', ['application_id' => $application_id, 'member_user_id' => $member_user_id, 'is_deleted' => 0]);
	}

	public function getFormApplicationMapping($application_id, $form_id) {
		return $this->getObject('FormApplicationMapping', ['application_id' => $application_id, 'form_id' => $form_id, 'is_deleted' => 0]);
	}
	
}