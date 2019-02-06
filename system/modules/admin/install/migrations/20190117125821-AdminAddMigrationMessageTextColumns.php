<?php

class AdminAddMigrationMessageTextColumns extends CmfiveMigration {

	public function up()
	{
		$this->addColumnToTable('migration', 'pretext', 'string', ['null'=>true, 'default'=>null]);
		$this->addColumnToTable('migration', 'posttext', 'string', ['null'=>true, 'default'=>null]);
		$this->addColumnToTable('migration', 'description', 'string', ['null'=>true, 'default'=>null]);
	}
	public function down() {
		// DOWN
		$this->removeColumnFromTable('migration', 'pretext');
		$this->removeColumnFromTable('migration', 'posttext');
		$this->removeColumnFromTable('migration', 'description', 'string');
	}
}
