<?php

class FileAddingIsPublicToAttachments extends CmfiveMigration {

	public function up() {
		$this->addColumnToTable("attachment", "is_public", "boolean", ["default" => false]);
	}

	public function down() {
		$this->removeColumnFromTable("attachment", "is_public");
	}
}
