<?php

function removewidget_ALL(Web $w) {

	$p = $w->pathMatch("origin", "id");// "source", "widget");

	$widget = $w->Widget->getWidgetById($p["id"]);//, $p["source"], $p["widget"]);
	if (empty($widget->id)) {
		$w->error("Widget not found", "/{$p['origin']}");
	}

	$widget->delete();

	$w->msg("Widget removed", "/{$p['origin']}");

}