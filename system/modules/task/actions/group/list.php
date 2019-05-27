<?php

/**
 * List task groups
 */
function list_GET(Web $w) {
	$w->setLayout('layout-f6');
	History::add('Task Group List');

	$w->Task->getTaskGroups();
}
