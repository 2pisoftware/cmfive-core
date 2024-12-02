<?php

class WidgetService extends DbService {

    public function getWidget($destination, $source, $widget) {
        return $this->getObject("WidgetConfig", array(
            "destination_module" => $destination, 
            "source_module" => $source, 
            "widget_name" => $widget,
            "is_deleted" => 0)
        );
    }

    public function getAll() {
        return $this->getObjects("WidgetConfig", array("is_deleted" => 0));
    }

    public function getWidgetsForModule($destination_module, $user_id) {
        return $this->getObjects("WidgetConfig", array("user_id" => $user_id, "destination_module" => $destination_module, "is_deleted" => 0));
    }

    public function getWidgetNamesForModule($module) {
        $w = $this->w;
        $user = AuthService::getInstance($w)->user();
        
        return array_filter($this->w->moduleConf($module, "widgets") ? : [], function($widget) use ($w, $user) {
            return $w->isClassActive($widget) && (new $widget($w))->canView($user);
        });
    }

    public function getWidgetById($id) {
        return $this->getObject("WidgetConfig", array("id" => $id, "is_deleted" => 0));
    }

}
