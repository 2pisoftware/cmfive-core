<?php

class AdminMigrationBatching extends CmfiveMigration {

	public function up() {
		// UP
		if ($this->hasTable("migration") && !$this->table("migration")->hasColumn("batch")) {
			$this->table("migration")->addColumn("batch", "integer")->save();
			$this->execute("update migration set batch=1");
		}
	}

	public function down() {
		// DOWN
		$this->hasTable("migration") && $this->table("migration")->hasColumn("batch") ? $this->table("migration")->removeColumn("batch") : null;
	}

}
