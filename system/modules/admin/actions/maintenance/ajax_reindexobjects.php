<?php

function ajax_reindexobjects_GET(Web $w) {
	if ($w->Auth->user() != null && $w->Auth->user()->is_admin  == 1) {
		$w->setLayout(null);
		$w->Search->reindexAll();

		echo $w->db->get('object_index')->count();
	} else {
		echo 0;
	}
	
}
