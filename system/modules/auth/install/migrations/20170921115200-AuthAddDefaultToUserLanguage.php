<?php

class AuthAddDefaultToUserLanguage extends CmfiveMigration {

	public function up() {
		// UP
		$this->changeColumnInTable("user", "language", "string", $options = ["null"=>true,"default"=>""]);
	}

	public function down() {
		// DOWN
	}

}
