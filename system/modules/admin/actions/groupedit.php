<?php
/**
* Display edit group dialog
*
* @param <type> $w
*/

use Html\Form\InputField\Text;

function groupedit_GET(Web $w)
{
	$w->setLayout('layout-bootstrap-5');
 
	$option = $w->pathMatch("group_id");

	$user = AuthService::getInstance($w)->getUser($option['group_id']);

	$w->out(HtmlBootstrap5::multiColForm([
		'Edit Group' => [
			[
				(new \Html\Form\InputField\Text([
					'id|name' => 'title',
					'label' => 'Group title',
					'value' => $user->login,
					'required' => true,
				]))
			]
		]
	], "/admin/groupedit/" . $option['group_id'], "POST", "Save"));

}

function groupedit_POST(Web $w)
{
	$option = $w->pathMatch("group_id");

	$user = AuthService::getInstance($w)->getUser($option['group_id']);
	$user->login = $_REQUEST['title'];
	$user->update();

	$w->msg("Group title updated", "/admin/groups");
}