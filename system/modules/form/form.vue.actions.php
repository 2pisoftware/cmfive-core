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

function get_members_GET(Web $w) {

	$w->setLayout(null);

	$output = ['success' => false, 'error' => '', 'data' => []];

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

}

function delete_form_GET(Web $w) {

}

function delete_member_GET(Web $w) {

}