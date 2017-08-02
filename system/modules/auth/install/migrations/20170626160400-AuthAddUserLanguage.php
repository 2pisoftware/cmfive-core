<?php

class AuthAddUserLanguage extends CmfiveMigration {

	public function up() {
		// UP
		if ($this->hasTable("user") && !$this->table("user")->hasColumn("language")) {
			$this->table("user")->addColumn("language", "text", ["null"=>true])->save();
		}
	}

	public function down() {
		// DOWN
		$this->hasTable("user") && $this->table("user")->hasColumn("language") ? $this->table("user")->removeColumn("language") : null;
	}

}