<?php

function installseed_GET(Web $w)
{

    $url = Request::string('url');

    if (empty($url)) {
        $w->msg('Cannot find requested seed', '/admin-migration');
    }

    $url = urldecode($url);
    $seeds = MigrationService::getInstance($w)->getSeedMigrations();

    if (empty($seeds)) {
        $w->msg("Cannot find seed migrations", '/admin-migration');
    }

    $runSeed = '';
    foreach ($seeds as $module => $available_seeds) {
        if (count($available_seeds) == 0) {
            continue;
        }

        foreach ($available_seeds as $path => $classname) {
            if (!MigrationService::getInstance($w)->migrationSeedExists($classname)) {
                if ($path == $url) {
                    require_once($path);
                    if (!class_exists($classname)) {
                        continue;
                    }
                    $runSeed = $classname;
                    $seed_obj = new $classname($w);
                    $seed_obj->seed();

                    $migration_seed = new MigrationSeed($w);
                    $migration_seed->name = $classname;
                    $migration_seed->insert();

                    break (2);
                }
            }
        }
    }

    $w->msg("Seed " . $runSeed . " has run", '/admin-migration#seed');
}
