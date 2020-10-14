<?php
// provide form by which to add members to an insight
function editMembers_GET(Web &$w) {

	//action title for adding new memeber
	$w->ctx('title', 'Add member');

	//retrieve correct insight to add new member to
	$insight_class = $w->request('insight_class');

	// get the list of report editors and admins
	$members1 = $w->Auth->getUsers("insight_owner");
	$members2 = $w->Auth->getUsers("insight_member");
	// merge into single array
	$members12 = array_merge($members1, $members2);

	// strip the dumplicates. dealing with an object so no quick solution
	$members = array();
	foreach ($members12 as $member) {
		if (!in_array($member, $members, true)) {
			$members[] = $member;
		}
	}

	// build form
	$addMemberForm = array(
	array("","hidden", "insight_class", $insight_class),
	array("Add Member","select","member",null,$members),
	array("With Role","select","role","",$w->Insight->getInsightPermissions()),
	);

	// sending the form to the 'out' function bypasses the template. 
	$w->out(Html::form($addMemberForm, 'insights-manageMembers'));
}

function editMembers_POST(Web $w) {

	//create a new memebr for insight
	$member = new InsightMembers($w);

	//use the fill function to fill input field data into properties with matching names
	$member->fill($_POST);
	
	// function for saving to database
	$member->insertOrUpdate();
	
	// the msg (message) function redirects with a message box
	$w->msg('Member Permissions Saved', '/insights/manageMembers'.$insight_class['insight_class']);
}
