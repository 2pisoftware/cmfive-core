<?php

function index_GET(Web $w) {

	$available = $w->Migration->getAvailableMigrations('all');
	$installed = $w->Migration->getInstalledMigrations('all');
	$seeds = $w->Migration->getSeedMigrations();

	$batched = [];
	if (!empty($installed)) {
		foreach($installed as $module => $install) {
			foreach($install as $migration_as_array) {
				$batched[$migration_as_array['batch']][] = $migration_as_array;
			}
		}
	}
	
	$not_installed = [];
	if (!empty($available)) {
		foreach($available as $module => $_available) {
			foreach($_available as $file => $class) {
				if (!$w->Migration->isInstalled($class['class_name'])) {
					$not_installed[$module][$file] = $class;
				}
			}
		}
	}
	
	// Sort by modules that have a migration first, then alphabetically
	uksort($available, function($a, $b) use ($available) {
		
		if (count($available[$a]) > 0 && count($available[$b]) == 0) {
			return -1;
		} else if (count($available[$a]) == 0 && count($available[$b]) > 0) {
			return 1;
		} else {
			return strcmp($a, $b);
		}
	});

	//for each available module sort migrations.
	// sort first by installed migration id
	// then by time string in filename
	foreach ($available as $module) {
		
		if (count($module) > 0) {
			uksort($module, function($a, $b) use ($module, $w) {
				var_dump($module[$a]);
				//first sort by installed or not
				//then by istalled migration id
				//then sort by migration created timestamp in file name
				if ($w->Migration->isInstalled($module[$a]['class_name']) && !$w->Migration->isInstalled($module[$b]['class_name'])) {
					echo "a has run but not b</br>";
					return -1;
				} else if (!$w->Migration->isInstalled($a['class_name']) && $w->Migration->isInstalled($b['class_name'])) {
					echo "b has run but not a</br>";
					return 1;
				} else if ($w->Migration->isInstalled($a['class_name']) && $w->Migration->isInstalled($b['class_name'])) {
					//sort by installed id to get order of installation
					$a_migration = $w->Migration->getMigrationByClassname($a['class_name']);
					$b_migration = $w->Migration->getMigrationByClassname($b['class_name']);
					var_dump($a_migration);
					return $a_migration->id < $b_migration->id ? -1 : 1;
				} else {
					echo "neither run. comparing timestamp</br>";
					return $a['timestamp'] < $b['timestamp'] ? 1 : -1;
				}
			});
		}
		// var_dump($module);
	}

	
	$seeds = array_filter($seeds, function($available_seeds) {
		return count($available_seeds) > 0;
	});
	
	$w->ctx('batched', $batched);
	$w->ctx('not_installed', $not_installed);
	$w->ctx('installed', $installed);
	$w->ctx('available', $available);
	$w->ctx('seeds', $seeds);
}