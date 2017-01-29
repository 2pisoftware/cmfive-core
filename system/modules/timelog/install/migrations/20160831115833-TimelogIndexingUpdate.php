<?php

class TimelogIndexingUpdate extends CmfiveMigration {

	public function up() {
		// UP
		$this->addIndexToTable('timelog', 'user_id');
		$this->addIndexToTable('timelog', 'object_class');
		$this->addIndexToTable('timelog', 'object_id');
		$this->addIndexToTable('timelog', 'is_deleted');
	}

	public function down() {
		// DOWN
		$this->removeIndexFromTable('timelog', 'user_id');
		$this->removeIndexFromTable('timelog', 'object_class');
		$this->removeIndexFromTable('timelog', 'object_id');
		$this->removeIndexFromTable('timelog', 'is_deleted');
	}

}
