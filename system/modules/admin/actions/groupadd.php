<?php
/**
 * Display add group dialog
 *
 * @param <type> $w
 */

use Html\Form\InputField\Text;

function groupadd_GET(Web $w)
{

	$w->setLayout('layout-bootstrap-5');

	$w->out(HtmlBootstrap5::multiColForm([
        'New Group' => [
            [
				(new \Html\Form\InputField\Text([
				'id|name' => 'title',
				'label' => 'Group title',
				'required' => true,
				]))
            ]
		]],  "/admin/groupadd", "POST", "Save"));

}

function groupadd_POST(Web $w)
{
	$user = new User($w);
	$user->login = $_REQUEST['title'];
	$user->is_group = 1;
    $user->is_active = 1;
	$user->insert();

    if (!empty($user->id)) {
    	$w->msg("New group added", "/admin/groups");       
    } else {
        $w->msg("Unable to create group.", "/admin/groups");
    }
}
