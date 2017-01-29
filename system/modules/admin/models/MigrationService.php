<?php

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('MIGRATION_DIRECTORY') || define('MIGRATION_DIRECTORY', 'install' . DS . 'migrations');
defined('PROJECT_MODULE_DIRECTORY') || define('PROJECT_MODULE_DIRECTORY', 'modules');
defined('SYSTEM_MODULE_DIRECTORY') || define('SYSTEM_MODULE_DIRECTORY', 'system' . DS . 'modules');
defined('SEED_MIGRATION_DIRECTORY') || define('SEED_MIGRATION_DIRECTORY', MIGRATION_DIRECTORY . DS . 'seeds');

class MigrationService extends DbService {
	
	public static $_installed = []; 
	public static $_NEXT_BATCH;
	
	public function getAvailableMigrations($module_name) {
		$_this = $this;
		set_error_handler(function($errno, $errstr) use ($_this) {
			if (!(error_reporting() & $errno)) {
				return;
			}
			
			if ($errno === E_USER_ERROR) {
				// Check if error contains a db error message
				if (strpos($errstr, "does not exist in the database") !== FALSE) {
					// Run the admin migrations to install the migration table (the normal cause of this error)
					$_this->installInitialMigration();
					
					// Reload the page
					$_this->w->redirect($_SERVER["REQUEST_URI"]);
				}
			}
		});
		
		$availableMigrations = [];
		
		// Read all modules directories for any migrations that need to run
		if ($module_name === 'all') {
			foreach($this->w->modules() as $module) {
				$availableMigrations += $this->getMigrationsForModule($module);
			}
		} else {
			$availableMigrations = $this->getMigrationsForModule($module_name);
		}
		
		// restore_error_handler();
		
		return $availableMigrations;
	}
	
	public function getMigrationsForModule($module) {
		$availableMigrations = [];
		
		// Check modules folder
		$module_path = PROJECT_MODULE_DIRECTORY . DS . $module . DS . MIGRATION_DIRECTORY;
		$system_module_path = SYSTEM_MODULE_DIRECTORY . DS . $module . DS . MIGRATION_DIRECTORY;

		$migration_paths = [$module_path, $system_module_path];
		if (empty($availableMigrations[$module])) {
			$availableMigrations[$module] = [];
		}

		foreach($migration_paths as $migration_path) {
			if (is_dir(ROOT_PATH . DS . $migration_path)) {
				foreach(scandir(ROOT_PATH . DS . $migration_path) as $file) {
					if (!is_dir($file) && $file{0} !== '.') {
						$classname = explode('.', str_replace('-', '.', $file));
						if (!empty($classname[1])) {
							$availableMigrations[$module][$migration_path . DS . $file] = $classname[1];
						} else {
							$this->w->Log->error("Migration '" . $file . "' does not conform to naming convention");
						}
					}
				}
			}
		}
		return $availableMigrations;
	}
	
	public function getMigrationByClassname($classname) {
		return $this->getObject('Migration', ['classname' => $classname]);
	}
	
	public function isInstalled($classname) {
		if (empty(self::$_installed[$classname])) {
			self::$_installed[$classname] = $this->w->db->get('migration')->where('classname', $classname)->count() == 1;
		}
		return self::$_installed[$classname];
	}
	
	public function getInstalledMigrations($module) {
		$migrations_query = $this->w->db->get('migration');
		if (!empty($module) && $module !== "all") {
			$migrations_query->where('module', strtolower($module));
		}
		$migrations = $migrations_query->orderBy('dt_created ASC')->fetch_all();
		$migrationsInstalled = [];
		
		if (!empty($migrations)) {
			foreach($migrations as $migration) {
				$migrationsInstalled[$migration['module']][] = $migration;
			}
		}
		
		return $migrationsInstalled;
	}
	
	public function createMigration($module, $name) {
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
		} else if (is_dir(SYSTEM_MODULE_DIRECTORY . DS . $module)) {
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

class {$classname} extends CmfiveMigration {

	public function up() {
		// UP
		\$column = parent::Column();
		\$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);
	}

	public function down() {
		// DOWN
	}

}

MIGRATION;
		file_put_contents($directory . DS . MIGRATION_DIRECTORY . DS . $filename, $data);
		
		return "Migration created";
	}
	
	public function runMigrations($module, $filename = null) {
		$alreadyRunMigrations = $this->getInstalledMigrations($module);
		$availableMigrations = $this->getAvailableMigrations($module);

		// Return if there are no migrations to run
		if (empty($availableMigrations)) {
			return;
		}
		
		// Strip out any migrations that have already run
		if (!empty($alreadyRunMigrations)) {
			foreach($alreadyRunMigrations as $module => $alreadyRunMigrationList) {
				if (!empty($alreadyRunMigrationList)) { 
					foreach($alreadyRunMigrationList as $migrationsAlreadyRun) {
						if (array_key_exists($migrationsAlreadyRun['path'], $availableMigrations[$module])) {
							unset($availableMigrations[$module][$migrationsAlreadyRun['path']]);
						}
					}
				}
			}
		}
		
		// If filename is specified then strip out migrations that shouldnt be run
		if (strtolower($module) !== "all" && !empty($filename)) {
			$offset_index = 1;

			foreach($availableMigrations[$module] as $availableMigrationsPath => $availableMigrationsClass) {
				if (strpos($availableMigrationsPath, $filename) !== FALSE) {
					break;
				}
				$offset_index++;
			}
			
			$availableMigrations[$module] = array_slice($availableMigrations[$module], 0, $offset_index);
		}
		
		// Install migrations
		if (!empty($availableMigrations)) {
			$this->w->db->startTransaction();

			try {
				// Use MySQL for now
				$mysql_adapter = new \Phinx\Db\Adapter\MysqlAdapter([
					'connection' => $this->w->db,
					'name' => Config::get('database.database')
				]);
				
				$runMigrations = 0;
				foreach($availableMigrations as $module => $migrations) {
					if (empty($migrations)) {
						continue;
					}
					
					foreach($migrations as $migration_path => $migration) {
						if (file_exists(ROOT_PATH . '/' . $migration_path)) {
							include_once ROOT_PATH . '/' . $migration_path;

							// Class name must match filename after timestamp and hyphen 
							if (class_exists($migration)) {
								$this->w->Log->setLogger("MIGRATION")->info("Running migration: " . $migration);

								// Run migration UP
								$migration_class = (new $migration(1))->setWeb($this->w);
								$migration_class->setAdapter($mysql_adapter);
								$migration_class->up();

								// Insert migration record into DB
								$migration_object = new Migration($this->w);
								$migration_object->path = $migration_path;
								$migration_object->classname = $migration;
								$migration_object->module = strtolower($module);
								$migration_object->batch = $this->getNextBatchNumber();
								$migration_object->insert();
								
								$runMigrations++;
								$this->w->Log->setLogger("MIGRATION")->info("Migration has run");
							}
						}
					}
				}

				// Finalise transaction
				$this->w->db->commitTransaction();
				return count($runMigrations) . ' migration' . (count($runMigrations) == 1 ? ' has' : 's have') . ' run'; 
			} catch (Exception $e) {
				$this->w->out("Error with a migration: " . $e->getMessage() . "<br/>More info: " . var_export($e));
				$this->w->Log->setLogger("MIGRATION")->error("Error with a migration: " . $e->getMessage());
				$this->w->db->rollbackTransaction();
			}
		} else {
			return "No migrations to run!";
		}
	}
	
	public function getNextBatchNumber() {
		if (empty($this->_NEXT_BATCH)) {
			$current_no = $this->w->db->get("migration")->select()->select("batch")->orderBy("batch DESC")->limit("1")->fetch_element("batch");
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
	public function rollback($module, $filename) {
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
		
		$offset_index = 0;
		foreach($installed_migrations[$module] as $installed_module_migration) {
			if (strpos($installed_module_migration['path'], $filename) !== FALSE) {
				break;
			}
			$offset_index++;
		}
		
		$migrations_to_rollback = array_slice($installed_migrations[$module], $offset_index);
		
		// Attempt to rollback all migrations
		if (!empty($migrations_to_rollback)) {
			$this->w->db->startTransaction();

			try {
				// Use MySQL for now
				$mysql_adapter = new \Phinx\Db\Adapter\MysqlAdapter([
					'connection' => $this->w->db,
					'name' => Config::get('database.database')
				]);
				
				foreach($migrations_to_rollback as $migration) {
					if (file_exists(ROOT_PATH . '/' . $migration['path'])) {
						include_once ROOT_PATH . '/' . $migration['path'];

						// Class name must match filename after timestamp and hyphen 
						if (class_exists($migration['classname'])) {
							$this->w->Log->setLogger("MIGRATION")->info("Rolling back migration: " . $migration['id']);

							// Run migration UP
							$migration_class = new $migration['classname'](1);
                                                        $migration_class->setWeb($this->w);
							$migration_class->setAdapter($mysql_adapter);
							$migration_class->down();

							// Delete migration record from DB
							$migration_object = $this->getObjectFromRow("Migration", $migration);
							$migration_object->delete();

							$this->w->Log->setLogger("MIGRATION")->info("Migration has rolled back");
						}
					}
				}

				// Finalise transaction
				$this->w->db->commitTransaction();
				return count($migrations_to_rollback) . ' migration' . (count($migrations_to_rollback) == 1 ? ' has' : 's have') . ' rolled back'; 
			} catch (Exception $e) {
				$this->w->out("Error with a migration: " . $e->getMessage());
				$this->w->Log->setLogger("MIGRATION")->error("Error with a migration: " . $e->getMessage());
				$this->w->db->rollbackTransaction();
			}
		}
	}
	
	public function batchRollback() {
		
		// Get latest batch
		$batch_no = ($this->getNextBatchNumber() - 1);
		
		$migrations = $this->getObjects("Migration", ["batch" => $batch_no]);
		$migrations_rolled_back = 0;
		if (!empty($migrations)) {
			foreach($migrations as $migration) {
				$migrations_rolled_back++;
				$this->rollback($migration->module, $migration->path);
			}
		}
		
		return $migrations_rolled_back . " migration" . ($migrations_rolled_back == 1 ? '' : 's') . " rolled back";
	}
	
	public function installInitialMigration() {
		$migration = "AdminInitialMigration";
		$filename = "20151030134124-AdminInitialMigration.php";
		
		$directory = SYSTEM_MODULE_DIRECTORY . DS . "admin" . DS . MIGRATION_DIRECTORY;
		
		$mysql_adapter = new \Phinx\Db\Adapter\MysqlAdapter([
			'connection' => $this->w->db,
			'name' => Config::get('database.database')
		]);

		if (file_exists(ROOT_PATH . DS . $directory . DS . $filename)) {
			include_once ROOT_PATH . DS . $directory . DS . $filename;

			// Class name must match filename after timestamp and hyphen 
			if (class_exists($migration)) {
				$this->w->Log->setLogger("MIGRATION")->info("Running migration: " . $migration);

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

				$this->w->Log->setLogger("MIGRATION")->info("Initial migration has run");
				
				return true;
			}
		}
		
		return false;
	}

	public function getSeedMigrations() {
		$availableMigrations = [];
		
		// Read all modules directories for any migrations that need to run
		foreach($this->w->modules() as $module) {
			$availableMigrations += $this->getSeedMigrationsForModule($module);
		}
		
		return $availableMigrations;
	}
	
	public function getSeedMigrationsForModule($module) {
		$availableMigrations = [];
		
		// Check modules folder
		$module_path = PROJECT_MODULE_DIRECTORY . DS . $module . DS . SEED_MIGRATION_DIRECTORY;
		$system_module_path = SYSTEM_MODULE_DIRECTORY . DS . $module . DS . SEED_MIGRATION_DIRECTORY;

		$migration_paths = [$module_path, $system_module_path];
		if (empty($availableMigrations[$module])) {
			$availableMigrations[$module] = [];
		}

		foreach($migration_paths as $migration_path) {
			if (is_dir(ROOT_PATH . DS . $migration_path)) {
				foreach(scandir(ROOT_PATH . DS . $migration_path) as $file) {
					if (!is_dir($file) && $file{0} !== '.') {
						$classname = explode('.', str_replace('-', '.', $file));
						if (!empty($classname[0])) {
							$availableMigrations[$module][$migration_path . DS . $file] = $classname[0];
						} else {
							$this->w->Log->error("Migration '" . $file . "' does not conform to naming convention");
						}
					}
				}
			}
		}

		return $availableMigrations;
	}

	public function migrationSeedExists($name) {
		return $this->w->db->get('migration_seed')->where('name', $name)->count() > 0;
	}

	public function createMigrationSeed($module, $name) {

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

class {$name} extends CmfiveSeedMigration {

	public \$name = "{$name}";
	public \$description = "<Enter description here>";

	public function seed() {
		
	}

}

MIGRATION;
	
		file_put_contents($_SERVER['DOCUMENT_ROOT'] . DS . $path . DS . SEED_MIGRATION_DIRECTORY . DS . "$name.php", $data);
		
		return true;
	}

}
