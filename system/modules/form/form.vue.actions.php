<?php

/**
 * Generic get form application function for vue endpoints
 * Named as such to prevent collisions
 * 
 * @param  \Web   $w web object
 * @return FormApplication|null
 */
function getFormApplication_VUE(Web $w) {
	list($id) = $w->pathMatch('id');

	if (empty($id)) {
		throw new \Exception('Missing ID');
	}

	$application = $w->FormApplication->getFormApplication($id);
	if (empty($application->id)) {
		throw new \Exception('Application not found');
	}

	return $application;
}

function getResponse_VUE() {
	return ['success' => false, 'error' => '', 'data' => []];
}

function save_application_POST(Web $w) {

	$w->setLayout(null);

	$output = getResponse_VUE();
	$application = null;
	
	try {
		$application = getFormApplication_VUE($w);
	} catch (Exception $e) {
		$ouput['error'] = $e->getMessage();
		$w->out(json_encode($output));
		return;
	}

	$is_active = $w->request('is_active');
	$application->title = $w->request('title');
	$application->description = $w->request('description');
	$application->is_active = ($is_active === "true" || intval($is_active) === 1 ? 1 : 0);

	$application->update();

	$output['success'] = true;
	$w->out(json_encode($output));

}

function get_members_GET(Web $w) {

	$w->setLayout(null);

	$output = getResponse_VUE();
	$application = null;

	try {
		$application = getFormApplication_VUE($w);
	} catch (Exception $e) {
		$ouput['error'] = $e->getMessage();
		$w->out(json_encode($output));
		return;
	}

	$members = $application->getMembers();
	if (!empty($members)) {
		foreach($members as $member) {
			$output['data'][] = [
				'id' => $member->id,
				'member_user_id' => $member->member_user_id,
				'name' => $member->getName(),
				'role' => $member->role,
				'application_id' => $member->application_id
			];
		}
	}

	$output['success'] = true;
	$w->out(json_encode($output));

}

function get_forms_GET(Web $w) {

	$w->setLayout(null);

	$output = getResponse_VUE();
	$application = null;

	try {
		$application = getFormApplication_VUE($w);
	} catch (Exception $e) {
		$ouput['error'] = $e->getMessage();
		$w->out(json_encode($output));
		return;
	}

	$forms = $application->getForms();
	if (!empty($forms)) {
		foreach($forms as $form) {
			$output['data'][] = [
				'id' => $form->id,
				'title' => $form->title,
				'no_instances' => $form->countFormInstancesForObject($application)
			];
		}
	}

	$output['success'] = true;
	$w->out(json_encode($output));

}

function save_form_POST(Web $w) {

	$w->setLayout(null);

	$output = getResponse_VUE();

	// Validate data
	if (empty($_POST['application_id']) || empty($_POST['id'])) {
		$output['error'] = 'Missing data';
		$w->out(json_encode($output));
		return;
	}

	$application_id = intval($_POST['application_id']);
	$form_id = intval($_POST['id']);

	// Get application and validate
	$application = $w->FormApplication->getFormApplication($application_id);
	if (empty($application->id)) {
		$output['error'] = 'Application not found';
		$w->out(json_encode($output));
		return;
	}

	// Get form and validate
	$form = $w->Form->getForm($form_id);
	if (empty($form->id)) {
		$output['error'] = 'Form not found';
		$w->out(json_encode($output));
		return;
	}

	// Validate no existing mapping
	$existing_mapping = $w->FormApplication->getFormApplicationMapping($application_id, $form_id);
	if (empty($existing_mapping->id)) {
		$mapping = new FormApplicationMapping($w);
		$mapping->application_id = $application_id;
		$mapping->form_id = $form_id;
		$mapping->insert();
	}

	// Return
	$output['success'] = true;
	$w->out(json_encode($output));

}

function save_member_POST(Web $w) {

	$w->setLayout(null);

	$output = getResponse_VUE();

	// Validate data
	if (empty($_POST['application_id']) || empty($_POST['member_user_id']) || empty($_POST['role'])) {
		$output['error'] = 'Missing data';
		$w->out(json_encode($output));
		return;
	}

	$application_id = intval($_POST['application_id']);
	$member_user_id = intval($_POST['member_user_id']);

	$existing_record_id = '';
	if (!empty($_POST['id'])) {
		$existing_record_id = intval($_POST['id']);
	}

	// Get application and validate
	$application = $w->FormApplication->getFormApplication($application_id);
	if (empty($application->id)) {
		$output['error'] = 'Application not found';
		$w->out(json_encode($output));
		return;
	}

	// Get user and validate
	$user = $w->Auth->getUser($member_user_id);
	if (empty($user)) {
		$output['error'] = 'User not found';
		$w->out(json_encode($output));
		return;
	}

	// Validate role
	if (!in_array($_POST['role'], FormApplicationMember::$_roles)) {
		$output['error'] = 'Invalid role';
		$w->out(json_encode($output));
		return;
	}

	// Find/create
	if (!empty($existing_record_id)) {
		$application_member = $w->FormApplication->getObject("FormApplicationMember", $existing_record_id);
		if (empty($application_member->id)) {
			$output['error'] = 'Existing record not found';
			$w->out(json_encode($output));
			return;
		} else {
			$application_member->member_user_id = $member_user_id;
			$application_member->role = $w->request('role');
			$application_member->update();
		}
	} else {
		$application_member = new FormApplicationMember($w);
		$application_member->application_id = $application_id;
		$application_member->member_user_id = $member_user_id;
		$application_member->role = $w->request('role');
		$application_member->insert();
	}

	// Return
	$output['success'] = true;
	$w->out(json_encode($output));
}

function delete_form_GET(Web $w) {

	$w->setLayout(null);

	$output = getResponse_VUE();

	list($id, $form_id) = $w->pathMatch('id', 'form_id');

	if (empty($id) || empty($form_id)) {
		$output['error'] = 'Missing ID';
		$w->out(json_encode($output));
		return;
	}

	$application = $w->FormApplication->getFormApplication($id);
	if (empty($application->id)) {
		$output['error'] = 'Application not found';
		$w->out(json_encode($output));
		return;
	}

	$existing_mapping = $w->FormApplication->getFormApplicationMapping($application->id, $form_id);
	if (!empty($existing_mapping->id)) {
		$existing_mapping->delete();
	}

	$output['success'] = true;
	$w->out(json_encode($output));

}

function delete_member_GET(Web $w) {


	$w->setLayout(null);

	$output = getResponse_VUE();

	list($id, $member_id) = $w->pathMatch('id', 'member_id');

	if (empty($id) || empty($member_id)) {
		$output['error'] = 'Missing ID';
		$w->out(json_encode($output));
		return;
	}

	$application = $w->FormApplication->getFormApplication($id);
	if (empty($application->id)) {
		$output['error'] = 'Application not found';
		$w->out(json_encode($output));
		return;
	}

	$existing_member = $w->FormApplication->getObject('FormApplicationMember', $member_id);
	if (!empty($existing_member->id) && $existing_member->application_id == $id && $existing_member->is_deleted == 0) {
		$existing_member->delete();
	}

	$output['success'] = true;
	$w->out(json_encode($output));
	
}