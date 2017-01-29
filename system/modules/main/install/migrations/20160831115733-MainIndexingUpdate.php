<?php

class MainIndexingUpdate extends CmfiveMigration {

	public function up() {
		// UP
		$this->addIndexToTable('widget_config', 'user_id');
		$this->addIndexToTable('widget_config', 'is_deleted');
		
		$this->addIndexToTable('rest_session', 'user_id');
		$this->addIndexToTable('rest_session', 'is_deleted');
		
		$this->addIndexToTable('object_history', 'class_name');
		$this->addIndexToTable('object_history', 'object_id');
		
		$this->addIndexToTable('object_history_entry', 'history_id');
		
		$this->addIndexToTable('object_index', 'class_name');
		$this->addIndexToTable('object_index', 'object_id');
		
		$this->addIndexToTable('object_modification', 'table_name');
		$this->addIndexToTable('object_modification', 'object_id');
	}

	public function down() {
		// DOWN
		$this->removeIndexFromTable('widget_config', 'user_id');
		$this->removeIndexFromTable('widget_config', 'is_deleted');
		
		$this->removeIndexFromTable('rest_session', 'user_id');
		$this->removeIndexFromTable('rest_session', 'is_deleted');
		
		$this->removeIndexFromTable('object_history', 'class_name');
		$this->removeIndexFromTable('object_history', 'object_id');
		
		$this->removeIndexFromTable('object_history_entry', 'history_id');
		
		$this->removeIndexFromTable('object_index', 'class_name');
		$this->removeIndexFromTable('object_index', 'object_id');
		
		$this->removeIndexFromTable('object_modification', 'table_name');
		$this->removeIndexFromTable('object_modification', 'object_id');
	}

}
