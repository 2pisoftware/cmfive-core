<?php
/**
* Display edit group dialog
*
* @param <type> $w
*/
function groupedit_GET(Web $w)
{
	$option = $w->pathMatch("group_id");

	$user = $w->Auth->getUser($option['group_id']);

	$template['Edit Group'] = array(array(array(__("Group Title: "),"text","title",$user->login)));

	$w->out(Html::multiColForm($template,"/admin/groupedit/".$option['group_id'],"POST",__("Save")));

	$w->setLayout(null);
}

function groupedit_POST(Web $w)
{
	$option = $w->pathMatch("group_id");

	$user = $w->Auth->getUser($option['group_id']);
	$user->login = $_REQUEST['title'];
	$user->update();

	$w->msg(__("Group info updated!"), "/admin/groups");
}
