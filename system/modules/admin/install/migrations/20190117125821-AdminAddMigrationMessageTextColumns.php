<?php

class AdminAddMigrationMessageTextColumns extends CmfiveMigration {

	public $preText = "";
	public $postText = "";

	public function up()
	{
		$this->addColumnToTable('migration', 'pretext', 'string');
		$this->addColumnToTable('migration', 'posttext', 'string');
	}
	public function down() {
		// DOWN
		$this->removeColumnFromTable('migration', 'pretext');
		$this->removeColumnFromTable('migration', 'posttext');
	}
}
