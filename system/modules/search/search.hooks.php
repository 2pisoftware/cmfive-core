<?php

function search_admin_extra_navigation_items(Web $w) {
	if (AuthService::getInstance($w)->user()->is_admin == 1) {
        if (class_exists('menuLinkStruct') && $w->_layout == 'layout-bootstrap-5') {
            return [
                new MenuLinkStruct("Search Admin", "search/reindexpage"),
            ];
        }

		return [
			$w->menuLink("search/reindexpage", "Search Admin")
		];
	}
}