<?php

class AuthAddProfileImageToContact extends CmfiveMigration {

	public function up() {
		// UP - @TODO: Set blob medium correctly?
		if ($this->hasTable("contact") && !$this->table("contact")->hasColumn("profile_img")) {
			$this->table("contact")->addColumn("profile_img", "blob", ["null"=>true, "limit" => 16777215])->save();
		}

	}

	public function down() {
		// DOWN
		$this->hasTable("contact") && $this->table("contact")->hasColumn("profile_img") ? $this->table("contact")->removeColumn("profile_img") : null;
	}
}