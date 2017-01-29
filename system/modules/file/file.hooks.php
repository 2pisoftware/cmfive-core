<?php

function file_admin_extra_navigation_items(Web $w) {
	if ($w->Auth->user()->is_admin == 1) {
		return [
			$w->menuLink("file/deletedfiles", "Deleted files")
		];
	}
}