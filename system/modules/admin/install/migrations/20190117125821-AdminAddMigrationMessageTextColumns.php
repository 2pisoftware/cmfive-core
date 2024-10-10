<?php

class AdminAddMigrationMessageTextColumns extends CmfiveMigration
{

	public function up()
	{

		if ($this->hasTable('migration') && !$this->table("migration")->hasColumn("pretext")) {
			$this->addColumnToTable('migration', 'pretext', 'string', ['default' => null, 'null' => true]);
			$this->addColumnToTable('migration', 'posttext', 'string', ['default' => null, 'null' => true]);
			$this->addColumnToTable('migration', 'description', 'string', ['default' => null, 'null' => true]);
		}
	}

	public function down()
	{
		if ($this->hasTable('migration') && $this->table("migration")->hasColumn("pretext")) {
			$this->removeColumnFromTable('migration', 'pretext');
			$this->removeColumnFromTable('migration', 'posttext');
			$this->removeColumnFromTable('migration', 'description');
		}
	}

	public function preText()
	{
		return "";
	}

	public function postText()
	{
		return "";
	}

	public function description()
	{
		return "";
	}
}
