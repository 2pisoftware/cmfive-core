<?php

class MainRestrictableObjects extends CmfiveMigration {

	public function up() {
		if (!$this->hasTable("restricted_object_owner")) {
			$this->tableWithId("restricted_object_owner")
				->addStringColumn("object_name")
				->addIdColumn("object_id")
				->addIdColumn("owner_id")
				->addCmfiveParameters()
				->create();
		}

		if (!$this->hasTable("restricted_object_access_whitelist")) {
			$this->tableWithId("restricted_object_access_whitelist")
				->addStringColumn("object_name")
				->addIdColumn("object_id")
				->addIdColumn("user_id")
				->addCmfiveParameters()
				->create();
		}
	}

	public function down() {
		$this->hasTable("restricted_object_owner") ? $this->dropTable("restricted_object_owner") : null;		
		$this->hasTable("restricted_object_access_whitelist") ? $this->dropTable("restricted_object_access_whitelist") : null;
	}

	public function description() {
		return "Adds the ability for objects to be able to be restricted from Users independant of their role";
	}
}
