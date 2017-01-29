<?php 

class AdminMigrationSeed extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		if (!$this->hasTable('migration_seed')) {
			$this->table('migration_seed', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('name', 'string')
				->addCmfiveParameters()
				->create();
		}
	}

	public function down() {
		$this->hasTable('migration_seed') && $this->dropTable('migration_seed');
 	}
 	
}