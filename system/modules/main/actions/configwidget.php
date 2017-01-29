<?php

function configwidget_GET(Web $w) {

	$p = $w->pathMatch("origin", "id");// "origin", "source", "widget");
	// $widget = $w->Widget->getWidget($p["origin"], $p["source"], $p["widget"]);
	$widget = $w->Widget->getWidgetById($p["id"]);
	// $widgetname = $p["widget"];

	if (empty($widget->id)) {
		$w->error("Widget not found", "/{$p['origin']}");
	}
	$widgetname = $widget->widget_name;
	$widget_config = null;
	if (class_exists($widgetname)) {
		$widget_config = new $widgetname($w, $widget);
	}

	if (!empty($widget_config)) {
		$w->out(Html::multiColForm($widget_config->getSettingsForm(), "/main/configwidget/{$p['origin']}/{$p['id']}"));// {$p['origin']}/{$p['source']}/{$p['widget']}"));
	} else {
		$w->out("Could not find widget class ({$widgetname})");
	}
}

function configwidget_POST(Web $w) {

	$p = $w->pathMatch("origin", "id");// "origin", "source", "widget");
	// $widget = $w->Widget->getWidget($p["origin"], $p["source"], $p["widget"]);
	$widget = $w->Widget->getWidgetById($p["id"]);
	// $widgetname = $p["widget"];

	if (empty($widget->id)) {
		$w->error("Widget not found", "/{$p['origin']}");
	}

	$vars = $_POST;
	unset($vars[CSRF::getTokenID()]);
	
	$widget->custom_config = json_encode($vars);
	$widget->update();

	$w->msg("Widget updated", "/{$p['origin']}");
}