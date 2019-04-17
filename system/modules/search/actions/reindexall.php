<?php
function reindexall_GET(Web $w) {
	$w->Search->reindexAll();
	$w->Search->reindexAllFulltextIndex();
	$w->msg("Objects have been reindexed", "/search/reindexpage");
}