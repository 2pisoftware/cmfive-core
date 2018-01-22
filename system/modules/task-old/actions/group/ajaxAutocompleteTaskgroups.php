<?php

function ajaxAutocompleteTaskgroups_GET(Web $w) {
	$w->setLayout(null);
	$term = $w->request("term");
	
	$taskgroups = $w->Task->getObjects("TaskGroup", ["title LIKE ?" => "%{$term}%"]);
	$return_data = [];
	if (!empty($taskgroups)) {
		$taskgroups = array_filter($taskgroups, function($taskgroup) use ($w) {
			return $taskgroup->canView($w->Auth->user());
		});
		
		if (!empty($taskgroups)) {
			foreach($taskgroups as $taskgroup) {
				$return_data[] = ["label" => $taskgroup->getSelectOptionTitle(), "value" => $taskgroup->getSelectOptionValue()];
			}
		}
	}
	
	echo json_encode($return_data);
}