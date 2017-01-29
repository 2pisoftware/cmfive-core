<?php

function installseed_GET(Web $w) {

	$url = $w->request('url');

	if (empty($url)) {
		$w->msg('Cannot find requested seed', '/admin-migration');
	}

	$url = urldecode($url);
	$seeds = $w->Migration->getSeedMigrations();

	if (empty($seeds)) {
		$w->msg("Cannot find seed migrations", '/admin-migration');
	}

	$runSeed = '';
	foreach($seeds as $module => $available_seeds) {
		if (count($available_seeds) == 0) {
			continue;
		}

		foreach($available_seeds as $path => $classname) {
			if (!$w->Migration->migrationSeedExists($classname)) {
				if ($path == $url) {
					require_once($path);
					$runSeed = $classname;
					$seed_obj = new $classname($w);
					$seed_obj->seed();

					$migration_seed = new MigrationSeed($w);
					$migration_seed->name = $classname;
					$migration_seed->insert();

					break(2);
				}
			}
		}
	}

	$w->msg("Seed " . $runSeed . " has run", '/admin-migration#seed');

}