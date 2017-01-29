<?php
function phpinfo_GET(Web $w) {
	$w->setLayout(null);
	phpinfo();
}
