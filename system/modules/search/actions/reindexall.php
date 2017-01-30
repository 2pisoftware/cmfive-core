<?php
function reindexall_GET(Web $w) {
	$w->Search->reindexAll();
	$w->out(__("Done"));
}
