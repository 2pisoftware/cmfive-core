<?php
function index_ALL(Web &$w) {
	$w->Admin->navigation($w,__("Dashboard"));
	$w->ctx("currentUsers",$w->Audit->getLoggedInUsers());  
}
