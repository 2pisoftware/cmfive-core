<?php
function reindexall_GET(Web $w) {

	ini_set('max_execution_time', 0);

	SearchService::getInstance($w)->reindexAll();
	SearchService::getInstance($w)->reindexAllFulltextIndex();
	$w->msg("Objects have been reindexed", "/search/reindexpage");
}