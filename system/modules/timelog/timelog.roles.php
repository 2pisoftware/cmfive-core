<?php

function role_timelog_user_allowed($w, $path) {
	return startsWith($path, "timelog");
}
