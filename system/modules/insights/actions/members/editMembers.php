<?php
// provide form by which to add members to an insight
function editMembers_GET(Web &$w) {

	//action title for adding new memeber
	$w->ctx('title', 'Add member');

	//retrieve correct insight to add new member to
	$insight_class = $w->request('insight_class');

	// get the list of users that can be added to the insight
	$memberstoadd = AuthService::getInstance($w)->getUsers("insight_member");

	// strip the dumplicates. dealing with an object so no quick solution
	$members = array();
	foreach ($memberstoadd as $member) {
		if (!in_array($member, $members, true)) {
			$members[] = $member;
		}
	}

	// build form
	$addMemberForm = array(
	array("","hidden", "insight_class_name", $insight_class),
	array("Add Member","select","user_id",null,$members),
	array("With Role","select","type","",$w->Insight->getInsightPermissions()),
	);

	// sending the form to the 'out' function bypasses the template. 
	$w->out(Html::form($addMemberForm, '/insights-members/editMembers'));
}

function editMembers_POST(Web $w) {

	//create a new memebr for insight
	$member = new InsightMembers($w);

	//use the fill function to fill input field data into properties with matching names
	$member->fill($_POST);
	
	// function for saving to database
	$member->insertOrUpdate();
	
	// the msg (message) function redirects with a message box
	$w->msg('Member Permissions Saved', '/insights/manageMembers?insight_class='.$member->insight_class_name);
}
