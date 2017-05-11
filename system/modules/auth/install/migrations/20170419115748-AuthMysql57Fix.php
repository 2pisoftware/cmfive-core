<?php

class AuthMysql57Fix extends CmfiveMigration {

	public function up() {
		// UP
		$this->changeColumnInTable('group_user', 'is_active', 'boolean', ['default' => 1]);
	}

	public function down() {
		// DOWN
	}

}
