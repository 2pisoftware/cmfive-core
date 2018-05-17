<?php

class AuthTwoFActorAuthentication extends CmfiveMigration {

	public function up() {
		$this->addColumnToTable("user", "active_2fa", "boolean", ["null" => true]);
		$this->addColumnToTable("user", "secret_2fa", "string", ["null" => true]);
	}
	
	public function down() {
		$this->removeColumnFromTable("user", "active_2fa");
		$this->removeColumnFromTable("user", "secret_2fa");
	}
}