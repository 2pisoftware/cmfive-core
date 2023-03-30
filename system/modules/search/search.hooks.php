<?php

function search_admin_extra_navigation_items(Web $w) {
	if (AuthService::getInstance($w)->user()->is_admin == 1) {
		return [
			$w->menuLink("search/reindexpage", "Search Admin")
		];
	}
}