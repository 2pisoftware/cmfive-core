<?php

class FileFixMissingParentId extends CmfiveMigration {

	public function up() {
		// UP
		$table = $this->table('attachment');
		if ($table->hasColumn('obj_id')) {
			$table->renameColumn("obj_id", "parent_id");
		}
	}

	public function down() {
		// DOWN
		$table = $this->table('attachment');
		if ($table->hasColumn('parent_id')) {
			$table->renameColumn("parent_id", "obj_id");
		}
	}

}
