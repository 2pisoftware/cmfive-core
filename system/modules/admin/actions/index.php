<?php
function index_ALL(Web &$w) {
	$w->Admin->navigation($w,"Dashboard");
	$w->ctx("currentUsers",$w->Audit->getLoggedInUsers());  
}
