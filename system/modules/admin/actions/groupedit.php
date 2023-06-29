<?php
/**
* Display edit group dialog
*
* @param <type> $w
*/
function groupedit_GET(Web $w)
{
	$w->setLayout('layout-bootstrap-5');

	$option = $w->pathMatch("group_id");

	$user = AuthService::getInstance($w)->getUser($option['group_id']);

	$template['Edit Group'] = array(array(array("Group Title: ","text","title",$user->login)));

	$w->out(HtmlBootstrap5::multiColForm($template,"/admin/groupedit/".$option['group_id'],"POST","Saved"));

}

function groupedit_POST(Web $w)
{
	$option = $w->pathMatch("group_id");

	$user = AuthService::getInstance($w)->getUser($option['group_id']);
	$user->login = $_REQUEST['title'];
	$user->update();

	$w->msg("Group title updated", "/admin/groups");
}
