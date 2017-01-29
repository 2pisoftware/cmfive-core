<?php namespace System\Modules\Form;

function listform(\Web $w, $params) {
	
	$w->ctx("redirect_url", $params['redirect_url']);
	$w->ctx("form", $params['form']);
	$w->ctx("instances", $w->Form->getFormInstancesForFormAndObject($params['form'], $params['object']));
	$w->ctx("object", $params['object']);
	
}