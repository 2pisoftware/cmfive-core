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
				'name' => $member->getName(),
				'role' => $member->role
			];
		}
	}

	$output['success'] = true;
	$w->out(json_encode($output));

}

function get_forms_GET(Web $w) {
	
	$w->setLayout(null);

	try {
		$application = getFormApplication_VUE($w);
	} catch (Exception $e) {
		$w->out(json_encode(['error' => $e->getMessage()]));
		return;
	}
}

function save_form_GET(Web $w) {

}

function save_form_POST(Web $w) {

}

function save_member_GET(Web $w) {

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
	$application_member = $w->FormApplication->getFormApplicationMember($application_id, $member_user_id);
	if (empty($application_member->id)) {
		$application_member = new FormApplicationMember($w);
		$application_member->application_id = $application_id;
		$application_member->member_user_id = $member_user_id;
	}

	$application_member->role = $_POST['role'];
	$application_member->insertOrUpdate();

	// Return
	$output['success'] = true;
	$w->out(json_encode($output));
}

function delete_form_GET(Web $w) {

}

function delete_member_GET(Web $w) {

}