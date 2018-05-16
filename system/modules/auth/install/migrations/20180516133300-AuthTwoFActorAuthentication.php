<?php

class AuthTwoFActorAuthentication extends CmfiveMigration {

	public function up() {
		$this->addColumnToTable("user", "active_2fa", "boolean", ["null" => true]); // "default" => 0
		//$this->addColumnToTable("user", "code_2fa", "string", ["null" => true]);
	}
	
	public function down() {
		$this->removeColumnFromTable("user", "active_2fa");
		//$this->removeColumnFromTable("user", "code_2fa");
	}
}