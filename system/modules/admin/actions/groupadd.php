<?php
/**
 * Display add group dialog
 *
 * @param <type> $w
 */

use Html\Form\InputField\Text;

function groupadd_GET(Web $w)
{
	$template['New Group'] = array(array(array("Group Title: ","text","title")));
	$validation = ['title' => ['required']];
	/*
	$template = [
				'New Group' => [ 
					(new \Html\Form\InputField\Text([
				'id|name' => 'title',
				'value' => ' ',
				'label' => 'Group title',
				'required' => true,
			]))
					]
				];
*/
	$w->out(Html::multiColForm($template,  "/admin/groupadd","POST","Save", null, null, null, "_self", true, $validation));

	$w->setLayout(null);
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
