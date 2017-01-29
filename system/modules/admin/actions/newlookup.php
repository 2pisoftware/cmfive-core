<?php
function newlookup_POST(Web &$w) {
	$w->Admin->navigation($w,"Lookup");

	$_REQUEST['type'] = ($_REQUEST['ntype'] != "") ? $_REQUEST['ntype'] : $_REQUEST['type'];

	$err = "";
	if ($_REQUEST['type'] == "")
	$err = "Please add select or create a TYPE<br>";
	if ($_REQUEST['code'] == "")
	$err .= "Please enter a KEY<br>";
	if ($_REQUEST['title'] == "")
	$err .= "Please enter a VALUE<br>";
	if ($w->Admin->getLookupbyTypeCode($_REQUEST['type'],$_REQUEST['code']))
	$err .= "Type and Key combination already exists";

	if ($err != "") {
		$w->error($err,"/admin/lookup/?tab=2");
	}
	else {
		$lookup = new Lookup($w);
		$lookup->fill($_REQUEST);
		$lookup->insert();

		$w->msg("Lookup Item added","/admin/lookup/");
	}
}