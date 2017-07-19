<?php namespace System\Modules\Tag;

function displayTags(\Web $w, $params = []) {
	
	if (empty($params['object'])) {
		return;
	}
	
	$w->enqueueStyle(['name' => 'selectize-css', 'uri' => '/system/modules/tag/assets/lib/selectize.js/dist/css/selectize.css', 'weight' => 300]);
	$w->enqueueScript(['name' => 'selectize-js', 'uri' => '/system/modules/tag/assets/lib/selectize.js/dist/js/standalone/selectize.min.js', 'weight' => 300]);
	
	$w->ctx('object', $params['object']);
	
	$w->ctx('tags', $w->Tag->getTagsByObject($params['object']->id, get_class($params['object'])));
	
}