<?php 

class AuthExternalUser extends CmfiveMigration {

	public function up() {
		$this->addColumnToTable('user', 'is_external', 'boolean', ['default' => 0]);
	}

	public function down() {
		$this->removeColumnFromTable('user', 'is_external');
	}

}