<?php
function reindexall_GET(Web $w) {

	ini_set('max_execution_time', 0);

	$w->Search->reindexAll();
	$w->Search->reindexAllFulltextIndex();
	$w->msg("Objects have been reindexed", "/search/reindexpage");
}