<?php
// provide form by which to add members to an insight
function editMembers_GET(Web &$w) {

    //We now need to check if we are adding a new members or editing an existing member
	//We will use pathmatch to retrieve a member id from the yrl
	$p = $w->pathMatch('id');
	//if the id exists we will retrieve the data for that member. Otherwise we will add a new member
	$member = !empty($p['id']) ? InsightService::getInstance($w)->getMemeberForId($p['id']) : new InsightMembers($w);

	//action title for adding new memeber and editing existing memeber
	$w->ctx('title', !empty($p['id']) ? 'Edit member' : 'Add new member');

	//retrieve correct insight to add new member to
	$insight_class_name = $w->request('insight_class');

	// get the list of users that can be added to the insight
	$userstoadd = AuthService::getInstance($w)->getUsers();

	// strip the dumplicates. dealing with an object so no quick solution
	$users = array();
	foreach ($userstoadd as $user) {
		if (!in_array($user, $users, true)) {
			if (!InsightService::getInstance($w)->IsMember($insight_class_name, $user->id)){
				$users[] = $user;
			}
		}
	}

	// build form
	$addMemberForm = array(
	array("","hidden", "insight_class_name", $insight_class_name),
	//We need to change the form data to reflect the member being edited
	array("Add Member","select","user_id",null,$member->id),
	array("With Role","select","type",$member->type,$w->Insight->getInsightPermissions()),
	);

	//if we are editing an existing meber we need to send the id to the post method
	if (!empty($p['id'])) {
		$postUrl = '/insights-members/editMembers/' . $member->id;
	} else {
		$postUrl = '/insights-members/editmembers';
	}

	// sending the form to the 'out' function bypasses the template. 
	$w->out(Html::multiColForm($addMemberForm, $postUrl));
}

function editMembers_POST(Web $w) {

	//As in the get function we need to check if we are editing an exisiting member
	$p = $w->pathMatch('id');
	$member = !empty($p['id']) ? InsightService::getInstance($w)->GetMemberForId($p['id']) : new InsightMembers($w);

	//use the fill function to fill input field data into properties with matching names
	$member->fill($_POST);
	
	// function for saving to database
	$member->insertOrUpdate();
	
	// the msg (message) function redirects with a message box
	$w->msg('Member Permissions Saved', '/insights/manageMembers?insight_class='.$member->insight_class_name);
}