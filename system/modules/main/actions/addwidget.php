<?php

function addwidget_GET(Web $w) {

	$p = $w->pathMatch("module");
	$module = $p["module"];

	$modulelist = $w->modules();
	$modules = array_filter($modulelist, function($module) use (&$w) {
		$names = $w->Widget->getWidgetNamesForModule($module);
		return !empty($names);
	});

	$form = array(__("Add a widget") =>
		array(
			// array(array("Add widget for", "select", "destination_module", $module, $w->modules())),
			array(array(__("Source module"), "select", "source_module", null, $modules)),
			array(array(__("Widget Name"), "select", "widget_name", null, array()))
		)
	);

	$w->ctx("widgetform", Html::multiColForm($form, "/main/addwidget/{$module}", "POST", __("Add")));
}

function addwidget_POST(Web $w) {

	$p = $w->pathMatch("module");
	$module = $p["module"];
	// $id = $p["id"];

	// $widget = $w->Widget->getWidget($_POST["destination_module"], $_POST["source_module"], $_POST["widget_name"]);
	// $widget = $w->Widget->getWidgetByID($)
	// if (null !== $widget) {
	// 	$w->error("This entry already exists!", "/{$module}/index");
	// }

	$widget = new WidgetConfig($w);
	$widget->destination_module = $module;
	$widget->fill($_POST);
	$widget->user_id = $w->Auth->user()->id;
	$response = $widget->insert();

	if ($response === true) {
		$w->msg(__("Widget Added"), "/{$module}/index");
	} else {
		$w->error(__("Could not add widget"), "/{$module}/index");
	}
}
