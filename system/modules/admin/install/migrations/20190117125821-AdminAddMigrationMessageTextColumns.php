<?php

class AdminAddMigrationMessageTextColumns extends CmfiveMigration {

	public function up()
	{
		$this->addColumnToTable('migration', 'pretext', 'string');
		$this->addColumnToTable('migration', 'posttext', 'string');
		$this->addColumnToTable('migration', 'description', 'string');
	}
	public function down() {
		// DOWN
		$this->removeColumnFromTable('migration', 'pretext');
		$this->removeColumnFromTable('migration', 'posttext');
		$this->removeColumnFromTable('migration', 'description', 'string');
	}
}
