<?php

class AdminAddMigrationMessageTextColumns extends CmfiveMigration {

	public function up() {
		$this->addColumnToTable('migration', 'pretext', 'string', ['default' => null, 'null' => true]);
		$this->addColumnToTable('migration', 'posttext', 'string', ['default' => null, 'null' => true]);
		$this->addColumnToTable('migration', 'description', 'string', ['default' => null, 'null' => true]);
	}

	public function down() {
		$this->removeColumnFromTable('migration', 'pretext');
		$this->removeColumnFromTable('migration', 'posttext');
		$this->removeColumnFromTable('migration', 'description');
	}

	public function preText() {
		return "";
	}

	public function postText() {
		return "";
	}

	public function description() {
		return "";
	}
}
