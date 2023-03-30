<?php namespace System\Modules\Main;

function listwidgets(\Web $w, $params) {
    $module = null;
	
    if (!empty($params["module"])) {
        $module = $params["module"];
    } else {
        $module = $w->_module ? : "main";
    }

    $widgets = \WidgetService::getInstance($w)->getWidgetsForModule($module, \AuthService::getInstance($w)->user()->id);
    $filter_widgets = array();
    
    if (!empty($widgets)) {

        // Filter out widgets in an inactive class
        $filter_widgets = array_filter($widgets, function($widget) use ($w) {
            return $w->isClassActive($widget->widget_name);
        });

        foreach ($filter_widgets as &$widget) {
            // Give each widget_config db object an instance of the matching class
            $widgetname = $widget->widget_name;
            $widget->widget_class = new $widgetname($w, $widget);
        }
    }

    $w->ctx("columns", 3);
    $w->ctx("widgets", $filter_widgets);
    $w->ctx("module", $module);
}