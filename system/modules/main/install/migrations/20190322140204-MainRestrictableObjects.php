<?php

class MainRestrictableObjects extends CmfiveMigration {

	public function up() {
		if (!$this->hasTable("restricted_object_user_link")) {
			$this->tableWithId("restricted_object_user_link")
				->addStringColumn("object_class")
				->addIdColumn("object_id")
				->addIdColumn("user_id")
				->addStringColumn("type")
				->addCmfiveParameters()
				->create();
		}
	}

	public function down() {
		$this->hasTable("restricted_object_user_link") ? $this->dropTable("restricted_object_user_link") : null;		
	}

	public function description() {
		return "Adds the ability for objects to be able to be restricted from Users independant of their role";
	}
}
