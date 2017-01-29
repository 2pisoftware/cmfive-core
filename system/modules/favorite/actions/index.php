<?php

function index_GET(Web $w) {
	$w->out($w->partial("display_favorites", null, "favorite"));
}