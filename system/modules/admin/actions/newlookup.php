<?php
function newlookup_POST(Web &$w) {
	$w->Admin->navigation($w,__("Lookup"));

	$_REQUEST['type'] = ($_REQUEST['ntype'] != "") ? $_REQUEST['ntype'] : $_REQUEST['type'];

	$err = "";
	if ($_REQUEST['type'] == "")
	$err = __("Please add select or create a TYPE<br>");
	if ($_REQUEST['code'] == "")
	$err .= __("Please enter a KEY<br>");
	if ($_REQUEST['title'] == "")
	$err .= __("Please enter a VALUE<br>");
	if ($w->Admin->getLookupbyTypeCode($_REQUEST['type'],$_REQUEST['code']))
	$err .= __("Type and Key combination already exists");

	if ($err != "") {
		$w->error($err,"/admin/lookup/?tab=2");
	}
	else {
		$lookup = new Lookup($w);
		$lookup->fill($_REQUEST);
		$lookup->insert();

		$w->msg(__("Lookup Item added"),"/admin/lookup/");
	}
}
