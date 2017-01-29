<?php

class FileIndexingUpdate extends CmfiveMigration {

	public function up() {
		// UP
		$this->addIndexToTable('attachment', 'parent_id');
		$this->addIndexToTable('attachment', 'modifier_user_id');
		$this->addIndexToTable('attachment', 'parent_table');
		$this->addIndexToTable('attachment', 'adapter');
		$this->addIndexToTable('attachment', 'is_deleted');
		
		$this->addIndexToTable('attachment_type', 'is_active');
	}

	public function down() {
		// DOWN
		$this->removeIndexFromTable('attachment', 'parent_id');
		$this->removeIndexFromTable('attachment', 'modifier_user_id');
		$this->removeIndexFromTable('attachment', 'parent_table');
		$this->removeIndexFromTable('attachment', 'adapter');
		$this->removeIndexFromTable('attachment', 'is_deleted');
		
		$this->removeIndexFromTable('attachment_type', 'is_active');
	}

}
