<?php namespace System\Modules\Form;

function listform(\Web $w, $params) {
	
	$w->enqueueScript(['name' => 'vue-js', 'uri' => '/system/templates/js/vue.js', 'weight' => 200]);

	$w->ctx("redirect_url", $params['redirect_url']);
	$w->ctx("form", $params['form']);
	$w->ctx("instances", $w->Form->getFormInstancesForFormAndObject($params['form'], $params['object']));
	$w->ctx("object", $params['object']);
	$w->ctx('display_only', !empty($params['display_only']) ? !!$params['display_only'] : false);
	
}