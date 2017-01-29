<?php

function ajaxClearCache_GET(Web $w) {
	if( $w->Auth->User() != null && $w->Auth->user()->is_admin ) {
		if(is_file(ROOT_PATH.'/cache/classdirectory.cache')) {
			unlink(ROOT_PATH.'/cache/classdirectory.cache');
		}
		if(is_file(ROOT_PATH.'/cache/config.cache')) {
			unlink(ROOT_PATH.'/cache/config.cache');
		}
	}
}