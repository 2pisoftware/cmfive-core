<?php

class FileAddDocxViewingFlag extends CmfiveMigration {

	public function up() {
		// UP
		$this->addColumnToTable("attachment", "dt_viewing_window", "datetime", ["null" => true, "default" => null]);

	}

	public function down() {
		// DOWN
		$this->removeColumnFromTable("attachment", "dt_viewing_window");
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
