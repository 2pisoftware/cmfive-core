<?php
/**
 * Display add group dialog
 *
 * @param <type> $w
 */
function groupadd_GET(Web $w)
{
	$template[__('New Group')] = array(array(array(__("Group Title: "),"text","title")));

	$w->out(Html::multiColForm($template,"/admin/groupadd","POST",__("Save")));

	$w->setLayout(null);
}

function groupadd_POST(Web $w)
{
	$user = new User($w);
	$user->login = $_REQUEST['title'];
	$user->is_group = 1;
	$user->insert();

	$w->msg(__("New group added!"), "/admin/groups");
}
