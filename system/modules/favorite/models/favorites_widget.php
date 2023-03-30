<?php

/**
 * widget for displaying favoritres 
 *
 * @author Steve Ryan, steve@2pisoftware.com, 2015
 */

class favorites_widget extends ModuleWidget {

    public static $widget_count = 0;

    public function getSettingsForm($current_settings = null) {
        return array();
    }

    public function display() {
		echo $this->w->partial("listfavorite", array('classname' => 'Favorite'), "favorites");
	}
	
	public function canView(User $user) {
		if (empty($user)) {
			$user = AuthService::getInstance($this->w)->user();
		}
		
		return $user->hasRole("favorites_user");
	}
}
