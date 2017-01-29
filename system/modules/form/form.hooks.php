<?php

function form_core_template_tab_headers(Web $w, $object) {
	if (empty($object)) {
		return;
	}
	$forms = $w->Form->getFormsMappedToObject($object);
	// Check and see if there are any forms mapped to the object
	if ($w->Form->areFormsMappedToObject($object)) {
		$tabHeaders=[];
		foreach ($forms as $form) {
			$tabHeaders[]="<a href='#".toSlug($form->title)."'>$form->title <span class='secondary round label cmfive__tab-label'>" . $form->countFormInstancesForObject($object) . "</span></a>";
		}
		return implode("",$tabHeaders);	
	}
	return '';
}

function form_core_template_tab_content(Web $w, $params) {
	if (empty($params['object']) || empty($params['redirect_url'])) {
		return;
	}
	
	// Check and see if there are any forms mapped to the object
	$forms = $w->Form->getFormsMappedToObject($params['object']);
	
	$forms_list = '';
	if (!empty($forms)) {
		foreach($forms as $form) {
			$forms_list .= '<div id="'.toSlug($form->title).'">'.$w->partial("listform", [
				"form" => $form, 
				"redirect_url" => $params['redirect_url'], 
				'object' => $params['object']
			], "form"). '</div>';
		}
	}
	return $forms_list ;
}
