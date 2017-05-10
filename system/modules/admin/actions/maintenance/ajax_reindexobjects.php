<?php

function ajax_reindexobjects_GET(Web $w) {
	
	$w->setLayout(null);
	$w->Search->reindexAll();
	
	echo $w->db->get('object_index')->count();
	
}
