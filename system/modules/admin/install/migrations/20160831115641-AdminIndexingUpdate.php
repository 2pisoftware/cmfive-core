<?php

class AdminIndexingUpdate extends CmfiveMigration {

	public function up() {
		// UP
		$this->addIndexToTable('comment', 'obj_id');
		$this->addIndexToTable('comment', 'is_deleted');
		$this->addIndexToTable('template', 'is_deleted');
	}

	public function down() {
		// DOWN
		$this->removeIndexFromTable('comment', 'obj_id');
		$this->removeIndexFromTable('comment', 'is_deleted');
		$this->removeIndexFromTable('template', 'is_deleted');
	}

}
