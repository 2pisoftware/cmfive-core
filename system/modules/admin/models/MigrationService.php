<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('MIGRATION_DIRECTORY') || define('MIGRATION_DIRECTORY', 'install' . DS . 'migrations');
defined('PROJECT_MODULE_DIRECTORY') || define('PROJECT_MODULE_DIRECTORY', 'modules');
defined('SYSTEM_MODULE_DIRECTORY') || define('SYSTEM_MODULE_DIRECTORY', 'system' . DS . 'modules');
defined('SEED_MIGRATION_DIRECTORY') || define('SEED_MIGRATION_DIRECTORY', MIGRATION_DIRECTORY . DS . 'seeds');

class MigrationService extends DbService
{
    public static $_installed = [];
    public $_NEXT_BATCH;

    public function getAvailableMigrations($module_name)
    {
        $_this = $this;
        set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($_this) {
            if (!(error_reporting() & $errno)) {
                return;
            }

            if ($errno === E_USER_ERROR) {
                // Check if error contains a db error message
                if (strpos($errstr, "does not exist in the database") !== false) {
                    LogService::getInstance($this->w)->error("Error table not found. Running initial migration. [" . $errstr . "]");
                    // Run the admin migrations to install the migration table (the normal cause of this error)
                    $_this->installInitialMigration();

                    // Reload the page unless migrations are from CLI
                    if (array_key_exists('REQUEST_METHOD', $_SERVER)) {
                        $_this->w->redirect($_SERVER["REQUEST_URI"]);
                    }
                }
            }
        });

        $availableMigrations = [];

        // Read all modules directories for any migrations that need to run
        if ($module_name === 'all') {
            foreach ($this->w->modules() as $module) {
                $availableMigrations += $this->getMigrationsForModule($module);
            }
        } else {
            $availableMigrations = $this->getMigrationsForModule($module_name);
        }

        // restore_error_handler();

        return $availableMigrations;
    }

    public function getMigrationsForModule($module)
    {
        $availableMigrations = [];

        // Check modules folder
        $module_path = PROJECT_MODULE_DIRECTORY . DS . $module . DS . MIGRATION_DIRECTORY;
        $system_module_path = SYSTEM_MODULE_DIRECTORY . DS . $module . DS . MIGRATION_DIRECTORY;

        $migration_paths = [$module_path, $system_module_path];
        if (empty($availableMigrations[$module])) {
            $availableMigrations[$module] = [];
        }

        foreach ($migration_paths as $migration_path) {
            if (is_dir(ROOT_PATH . DS . $migration_path)) {
                foreach (scandir(ROOT_PATH . DS . $migration_path) as $file) {
                    if (!is_dir(ROOT_PATH . DS . $migration_path . DS . $file) && $file[0] !== '.') {
                        $classname = explode('.', str_replace('-', '.', $file));
                        if (!empty($classname[1])) {
                            if ($this->isInstalled($classname[1])) {
                                $mig = $this->getMigrationByClassname($classname[1]);
                                $availableMigrations[$module][$migration_path . DS . $file] = [
                                    'class_name' => $classname[1],
                                    'timestamp' => $classname[0],
                                    'description' => $mig->description,
                                    'pretext' => $mig->pretext,
                                    'posttext'  => $mig->posttext
                                ];
                            } else {
                                //Create instance of class
                                $migpath = $migration_path . DS . $file;
                                if (file_exists(ROOT_PATH . DS . $migpath)) {
                                    include_once ROOT_PATH . DS . $migpath;

                                    $migration_class = explode('-', $file)[1];
                                    $migration_class = preg_replace('/.php$/', '', $migration_class);
                                    if (class_exists($migration_class)) {
                                        $migration = (new $migration_class(1))->setWeb($this->w);
                                        $availableMigrations[$module][$migration_path . DS . $file] = [
                                            'class_name' => $classname[1],
                                            'timestamp' => $classname[0],
                                            'description' => $migration->description(),
                                            'pretext' => $migration->preText(),
                                            'posttext' => $migration->postText()
                                        ];
                                    }
                                }
                            }
                        } else {
                            LogService::getInstance($this->w)->error("Migration '" . $file . "' does not conform to naming convention");
                        }
                    }
                }
            }
        }
        return $availableMigrations;
    }

    public function getMigrationByClassname($classname)
    {
        return $this->getObject('Migration', ['classname' => $classname]);
    }

    public function isInstalled($classname)
    {
        if (empty(self::$_installed[$classname])) {
            self::$_installed[$classname] = $this->w->db->get('migration')->where('classname', $classname)->count() >= 1;
        }
        return self::$_installed[$classname];
    }

    public function getInstalledMigrations($module)
    {
        $migrations_query = $this->w->db->get('migration');
        if (!empty($module) && $module !== "all") {
            $migrations_query->where('module', strtolower($module));
        }
        $migrations = $migrations_query->orderBy('dt_created ASC')->fetchAll();
        $migrationsInstalled = [];

        if (!empty($migrations)) {
            foreach ($migrations as $migration) {
                $to_add = true;
                //var_dump($migration);
                if (array_key_exists($migration['module'], $migrationsInstalled)) {
                    foreach ($migrationsInstalled[$migration['module']] as $processed_migration) {
                        if ($migration['classname'] == $processed_migration['classname']) {
                            $to_add = false;
                        }
                    }
                    if ($to_add) {
                        $migrationsInstalled[$migration['module']][] = $migration;
                    }
                } else {
                    $migrationsInstalled[$migration['module']][] = $migration;
                }
            }
        }

        return $migrationsInstalled;
    }

    public function createMigration($module, $name)
    {
        if (empty($module) || !in_array($module, $this->w->modules())) {
            return 'Missing module or it doesn\'t exist';
        }

        $module = strtolower($module);
        if (empty($name)) {
            $name = "Migration";
        }

        $name = str_replace(' ', '', $name);

        // Find where the module is
        $directory = '';
        if (is_dir(PROJECT_MODULE_DIRECTORY . DS . $module)) {
            $directory = PROJECT_MODULE_DIRECTORY . DS . $module;
        } elseif (is_dir(SYSTEM_MODULE_DIRECTORY . DS . $module)) {
            $directory = SYSTEM_MODULE_DIRECTORY . DS . $module;
        } else {
            return 'Could not find module directory';
        }

        // Create migration directory if it doesn't exist
        if (!is_dir($directory . DS . MIGRATION_DIRECTORY)) {
            mkdir($directory . DS . MIGRATION_DIRECTORY, 0755, true);
        }

        // Create migration file
        $timestamp = date('YmdHis');
        $classname = ucfirst(strtolower($module)) . $name;
        $filename = $timestamp . '-' . $classname . '.php';
        $data = <<<MIGRATION
<?php

class {$classname} extends CmfiveMigration
{
    public function up()
    {
        // UP
        \$column = parent::Column();
        \$column->setName('id')
                ->setType('biginteger')
                ->setIdentity(true);

    }

    public function down()
    {
        // DOWN
    }

    public function preText()
    {
        return null;
    }

    public function postText()
    {
        return null;
    }

    public function description()
    {
        return null;
    }
}

MIGRATION;
        file_put_contents($directory . DS . MIGRATION_DIRECTORY . DS . $filename, $data);

        return "Migration created";
    }

    public function runMigrations($module, $filename = null, $ignoremessages = true, $continuingrunall = false)
    {
        // Check if migrations are being run from the batch menu
        if ($module == "all") {
            $prevpage = "batch";
        } else {
            $prevpage = "individual";
            // If not - set values of the migration that it should be migrating to
            $_module = $module;
            $_filename = $filename;
        }

        //if no migrations have run run initial migrations
        $this->w->db->setMigrationMode(true);
        if (!in_array('migration', $this->w->db->getAvailableTables()) || $this->w->db->get('migration')->select()->count() == 0) {
            LogService::getInstance($this->w)->setLogger("MIGRATION")->info("initial migration not run. Running initial migration");
            $this->installInitialMigration();
            $alreadyRunMigrations = $this->getInstalledMigrations($module);
        }

        $alreadyRunMigrations = $this->getInstalledMigrations($module);
        $availableMigrations = $this->getAvailableMigrations($module);

        // Return if there are no migrations to run
        if (empty($availableMigrations)) {
            return;
        }

        // Strip out any migrations that have already run
        if (!empty($alreadyRunMigrations)) {
            foreach ($alreadyRunMigrations as $module => $alreadyRunMigrationList) {
                if (!empty($alreadyRunMigrationList)) {
                    foreach ($alreadyRunMigrationList as $migrationsAlreadyRun) {
                        if (array_key_exists($migrationsAlreadyRun['path'], $availableMigrations[$module] ?? [])) {
                            unset($availableMigrations[$module][$migrationsAlreadyRun['path']]);
                        }
                    }
                }
            }
        }

        // If filename is specified then strip out migrations that shouldnt be run
        if (strtolower($module) !== "all" && !empty($filename)) {
            $offset_index = 1;
            $filename_parts = explode('.', $filename);
            $file_timestamp = (float)  $filename_parts[0];

            foreach ($availableMigrations[$module] as $availableMigrationsPath => $data) {
                //check module timestamp and remove available migrations with greater timestamp value
                $availableMigrationTimestamp = (float) $data['timestamp'];

                if ($file_timestamp < $availableMigrationTimestamp) {
                    unset($availableMigrations[$module][$availableMigrationsPath]);
                }
            }
        }

        // Install migrations
        $buffer = "";
        $installedBuffer = "";
        $uninstalledBuffer = "";
        if (!empty($availableMigrations)) {
            $this->w->db->setMigrationMode(1);

            // Use MySQL for now
            $mysql_adapter = new \Phinx\Db\Adapter\MysqlAdapter([
                'connection' => $this->w->db,
                'name' => Config::get('database.database')
            ]);

            $runMigrations = 0;
            foreach ($availableMigrations as $module => $migrations) {
                if (empty($migrations)) {
                    continue;
                }

                //sort module migrations
                uasort($migrations, function ($a, $b) {
                    return $a['timestamp'] > $b['timestamp'];
                });

                foreach ($migrations as $migration_path => $migration) {
                    // var_dump($migration_path);
                    // die;
                    if (file_exists(ROOT_PATH . DS . $migration_path)) {
                        include_once ROOT_PATH . DS . $migration_path;

                        // Class name must match filename after timestamp and hyphen
                        if (class_exists($migration['class_name'])) {
                            LogService::getInstance($this->w)->setLogger("MIGRATION")->info("Running migration: " . $migration['class_name']);

                            $this->w->db->startTransaction();
                            try {
                                // Set migration class
                                $migration_class = (new $migration['class_name'](1))->setWeb($this->w);
                                $migration_class->setAdapter($mysql_adapter);

                                if ($continuingrunall == true) {
                                    $ignoremessages = true;
                                }

                                // Check if the migration has a preText and the flag has not been enabled
                                if (!$ignoremessages) {
                                    if (!empty($migration_class->preText())) {
                                        $pathData = pathinfo($migration_path);
                                        $messageurl = "/admin-migration/migrationmessage?module=" . $module . "&filename=" . $pathData['filename'] . "&migmodule=" . $_module . "&migfilename=" . $_filename . "&path=" . $migration_path . "&prevpage=" . $prevpage;

                                        $batchedMigrations = [];
                                        foreach ($availableMigrations as $avmigration) {
                                            // If the avmigration array has elements in it that means it's part of the current batch
                                            if (is_array($avmigration) && count($avmigration) > 0) {
                                                //Add it to the batched migration variable
                                                $batchedMigrations[] = $avmigration;
                                            }
                                        }
                                        foreach ($batchedMigrations as $bmigrations) {
                                            foreach ($bmigrations as $bmigration) {
                                                $classname = $bmigration['class_name'];

                                                if ($this->isInstalled($classname)) {
                                                    // If the migration has been installed at it to the buffer to display this information on the event of the migrations being interupted by a pre text message
                                                    $installedBuffer .= $classname . "<br>";
                                                } else {
                                                    // These are the migrations that were halted due to the pre text message and weren't installed
                                                    $uninstalledBuffer .= $classname . "<br>";
                                                }
                                            }
                                        }
                                        if ($runMigrations > 0) {
                                            $msg = "<table style width='100%'><tr><td><center>" . $runMigrations . ' migration' . ($runMigrations == 1 ? ' has' : 's have') . ' run. <br>';

                                            $msg .= ($buffer != "" ? "<h5><strong>Post Migration Output:</strong></h5>" . $buffer . "</center></td>" : "<center>There was no post migration output</center>");

                                            $msg .= ($installedBuffer != "" ? " <td><strong>Migrations that were run and installed:</strong> <br>" . $installedBuffer . "<br>" : "");

                                            $msg .= ($uninstalledBuffer != "" ? "<strong>Migrations yet to run:</strong> <br>" . $uninstalledBuffer . "<br></td></tr></table>" : "");
                                        }
                                        $this->w->msg($msg, $messageurl);
                                    }
                                }

                                if ($continuingrunall == true) {
                                    $continuingrunall = false;
                                    $ignoremessages = false;
                                }

                                $migration_class->up();

                                // Insert migration record into DB
                                $migration_object = new Migration($this->w);
                                $migration_object->path = $migration_path;
                                $migration_object->classname = $migration['class_name'];
                                $migration_object->module = strtolower($module);
                                $migration_object->batch = $this->getNextBatchNumber();
                                $migration_object->pretext = $migration_class->preText();
                                $migration_object->posttext = $migration_class->postText();
                                $migration_object->description = $migration_class->description();
                                $migration_object->insert();

                                // If migration has a post text message, add it to the buffer
                                if (!empty($migration_class->postText())) {
                                    $buffer .= "<strong>" . $migration_object->classname . ":</strong> " . $migration_class->postText() . "<br>";
                                }
                                $runMigrations++;

                                $this->w->db->commitTransaction();
                                LogService::getInstance($this->w)->setLogger("MIGRATION")->info("Migration has run");
                            } catch (Exception $e) {
                                $this->w->db->rollbackTransaction();
                                if (defined('STDIN')) {
                                    echo "Error with a migration: " . $e->getMessage() . "<br/>More info: " . var_export($e);
                                } else {
                                    $this->w->out("Error with a migration: " . $e->getMessage() . "<br/>More info: " . var_export($e));
                                }
                                LogService::getInstance($this->w)->setLogger("MIGRATION")->error("Error with a migration: " . $e->getMessage());

                                // Skip current modules migrations
                                break;
                            }
                        }
                    }
                }
            }

            // Finalise transaction
            $this->w->db->setMigrationMode(false);

            return $runMigrations . ' migration' . ($runMigrations == 1 ? ' has' : 's have') . ' run. <br>' . ($buffer != "" ? "<h5><strong>Post Migration Output:</strong></h5>" . $buffer : "There was no post migration output");
        } else {
            $this->w->db->setMigrationMode(false);
            return "No migrations to run!";
        }
    }

    public function getNextBatchNumber()
    {
        if (empty($this->_NEXT_BATCH)) {
            $current_no = $this->w->db->get("migration")->select()->select("batch")->orderBy("batch DESC")->limit("1")->fetchElement("batch");
            $this->_NEXT_BATCH = !empty($current_no) ? $current_no + 1 : 1;
        }

        return $this->_NEXT_BATCH;
    }

    /**
     * Will rollback migrations for a given module up to (and including) the
     * given filename, if no filename is given this function will not run.
     *
     * The file name should be the migration filename minus the .php extension
     * to minimise the chance that an actual PHP file is served by the web server
     *
     * @param <string> $module
     * @param <string> $filename
     * @return <string> $response
     */
    public function rollback($module, $filename)
    {
        if (empty($module) || empty($filename)) {
            return "Missing parameters required for a rollback";
        }

        if (!in_array($module, $this->w->modules())) {
            return "Module doesn't exist";
        }

        $installed_migrations = $this->getInstalledMigrations($module);
        if (empty($installed_migrations[$module])) {
            return "There are no installed migrations to rollback";
        }

        //find id of filename migration and remove migrations from list with lower ids
        $file_migration_id = '';
        foreach ($installed_migrations[$module] as $installed_module_migration) {
            if (strpos($installed_module_migration['path'], $filename) !== false) {
                $file_migration_id = $installed_module_migration['id'];
            }
        }

        if ($file_migration_id == '') {
            return "Could not find migration in database";
        }
        foreach ($installed_migrations[$module] as $key => $installed_module_migration) {
            if ($file_migration_id > $installed_module_migration['id']) {
                unset($installed_migrations[$module][$key]);
            }
        }

        //sort installed migrations by id largest to smallest
        $migrations_to_rollback = $installed_migrations[$module];
        usort($migrations_to_rollback, function ($a, $b) {
            return $a['id'] - $b['id'];
        });

        // Attempt to rollback all migrations
        if (!empty($migrations_to_rollback)) {
            $this->w->db->startTransaction();
            //set migration mode
            $this->w->db->setMigrationMode(true);

            try {
                // Use MySQL for now
                $mysql_adapter = new \Phinx\Db\Adapter\MysqlAdapter([
                    'connection' => $this->w->db,
                    'name' => Config::get('database.database')
                ]);

                foreach ($migrations_to_rollback as $migration) {
                    if (file_exists(ROOT_PATH . DS . $migration['path'])) {
                        include_once ROOT_PATH . DS . $migration['path'];

                        // Class name must match filename after timestamp and hyphen
                        if (class_exists($migration['classname'])) {
                            LogService::getInstance($this->w)->setLogger("MIGRATION")->info("Rolling back migration: " . $migration['id']);

                            // Run migration UP
                            $migration_class = new $migration['classname'](1);
                            $migration_class->setWeb($this->w);
                            $migration_class->setAdapter($mysql_adapter);
                            $migration_class->down();

                            // Delete migration record from DB
                            $migration_object = $this->getObjectFromRow("Migration", $migration);
                            $migration_object->delete();

                            LogService::getInstance($this->w)->setLogger("MIGRATION")->info("Migration has rolled back");
                        }
                    }
                }

                // Finalise transaction
                $this->w->db->commitTransaction();
                $this->w->db->setMigrationMode(false);

                return count($migrations_to_rollback) . ' migration' . (count($migrations_to_rollback) == 1 ? ' has' : 's have') . ' rolled back';
            } catch (Exception $e) {
                $this->w->out("Error with a migration: " . $e->getMessage());
                LogService::getInstance($this->w)->setLogger("MIGRATION")->error("Error with a migration: " . $e->getMessage());
                $this->w->db->rollbackTransaction();
            }
        }
    }

    public function batchRollback()
    {
        // Get latest batch
        $batch_no = ($this->getNextBatchNumber() - 1);

        $migrations = $this->getObjects("Migration", ["batch" => $batch_no]);
        $migrations_rolled_back = 0;
        if (!empty($migrations)) {
            foreach ($migrations as $migration) {
                $migrations_rolled_back++;
                $this->rollback($migration->module, $migration->path);
            }
        }

        return $migrations_rolled_back . " migration" . ($migrations_rolled_back == 1 ? '' : 's') . " rolled back";
    }

    public function installInitialMigration()
    {
        $filenames = [
            "AdminInitialMigration" => "20151030134124-AdminInitialMigration.php",
            "AdminMigrationSeed" => "20170123091600-AdminMigrationSeed.php"
        ];

        $directory = SYSTEM_MODULE_DIRECTORY . DS . "admin" . DS . MIGRATION_DIRECTORY;

        $mysql_adapter = new \Phinx\Db\Adapter\MysqlAdapter([
            'connection' => $this->w->db,
            'name' => Config::get('database.database')
        ]);

        $count = 0;
        foreach ($filenames as $migration => $filename) {
            if (file_exists(ROOT_PATH . DS . $directory . DS . $filename)) {
                include_once ROOT_PATH . DS . $directory . DS . $filename;

                // Class name must match filename after timestamp and hyphen
                if (class_exists($migration)) {
                    LogService::getInstance($this->w)->setLogger("MIGRATION")->info("Running migration: " . $migration);

                    // Run migration UP
                    $migration_class = new $migration(1);
                    $migration_class->setAdapter($mysql_adapter);
                    $migration_class->up();

                    // Reload table list in DbPDO
                    $this->w->db->getAvailableTables();

                    // Insert migration record into DB
                    $migration_object = new Migration($this->w);
                    $migration_object->path = $directory . DS . $filename;
                    $migration_object->classname = $migration;
                    $migration_object->module = "admin";
                    $migration_object->batch = 1;
                    $migration_object->dt_created = time();
                    $migration_object->creator_id = 1;
                    $migration_object->insert();

                    LogService::getInstance($this->w)->setLogger("MIGRATION")->info("Initial migration has run");

                    $count++;
                }
            }
        }

        return count($filenames) == $count;
    }

    public function getSeedMigrations()
    {
        $availableMigrations = [];

        // Read all modules directories for any migrations that need to run
        foreach ($this->w->modules() as $module) {
            $availableMigrations += $this->getSeedMigrationsForModule($module);
        }

        return $availableMigrations;
    }

    public function getSeedMigrationsForModule($module)
    {
        $availableMigrations = [];

        // Check modules folder
        $module_path = PROJECT_MODULE_DIRECTORY . DS . $module . DS . SEED_MIGRATION_DIRECTORY;
        $system_module_path = SYSTEM_MODULE_DIRECTORY . DS . $module . DS . SEED_MIGRATION_DIRECTORY;

        $migration_paths = [$module_path, $system_module_path];
        if (empty($availableMigrations[$module])) {
            $availableMigrations[$module] = [];
        }

        foreach ($migration_paths as $migration_path) {
            if (is_dir(ROOT_PATH . DS . $migration_path)) {
                foreach (scandir(ROOT_PATH . DS . $migration_path) as $file) {
                    if (!is_dir($file) && $file[0] !== '.') {
                        $classname = explode('.', str_replace('-', '.', $file));
                        if (!empty($classname[0])) {
                            $availableMigrations[$module][$migration_path . DS . $file] = $classname[0];
                        } else {
                            LogService::getInstance($this->w)->error("Migration '" . $file . "' in " . $module . " does not conform to naming convention");
                        }
                    }
                }
            }
        }

        return $availableMigrations;
    }

    public function migrationSeedExists($name)
    {
        return $this->w->db->get('migration_seed')->where('name', $name)->count() > 0;
    }

    public function createMigrationSeed($module, $name)
    {
        // Check if its a system module
        $path = SYSTEM_MODULE_DIRECTORY . DS . $module;

        if (!is_dir($path)) {
            $path = PROJECT_MODULE_DIRECTORY . DS . $module;

            if (!is_dir($path)) {
                return false;
            }
        }

        // Create folder if it doesn't exist
        if (!is_dir($path . DS . SEED_MIGRATION_DIRECTORY)) {
            mkdir($path . DS . SEED_MIGRATION_DIRECTORY, 0755, true);
        }

        $data = <<<MIGRATION
<?php

class {$name} extends CmfiveSeedMigration
{
    public \$name = "{$name}";
    public \$description = "<Enter description here>";

    public function seed()
    {

    }

}

MIGRATION;

        file_put_contents($_SERVER['DOCUMENT_ROOT'] . DS . $path . DS . SEED_MIGRATION_DIRECTORY . DS . "$name.php", $data);

        return true;
    }
}
