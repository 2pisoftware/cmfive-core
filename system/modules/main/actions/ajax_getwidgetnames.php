<?php

function ajax_getwidgetnames_GET(Web $w) {

	$module = $w->request("source");
	if (!empty($module)) {
		$names = $w->Widget->getWidgetNamesForModule($module);
		echo json_encode($names);
	}

}