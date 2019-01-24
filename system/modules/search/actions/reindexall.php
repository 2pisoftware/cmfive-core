<?php
function reindexall_GET(Web $w) {
	$w->Search->reindexAll();
	$w->msg("Objects have been reindexed", "/search/reindexpage");
}