<?php

class FormIndexingUpdate extends CmfiveMigration {

	public function up() {
		// UP
		$this->addIndexToTable('form', 'is_deleted');
		
		$this->addIndexToTable('form_field', 'form_id');
		$this->addIndexToTable('form_field', 'ordering');
		$this->addIndexToTable('form_field', 'is_deleted');
		
		$this->addIndexToTable('form_value', 'form_instance_id');
		$this->addIndexToTable('form_value', 'form_field_id');
		$this->addIndexToTable('form_value', 'is_deleted');
		
		$this->addIndexToTable('form_field_metadata', 'form_field_id');
		$this->addIndexToTable('form_field_metadata', 'is_deleted');
		
		$this->addIndexToTable('form_instance', 'form_id');
		$this->addIndexToTable('form_instance', 'object_class');
		$this->addIndexToTable('form_instance', 'object_id');
		$this->addIndexToTable('form_instance', 'is_deleted');
		
		$this->addIndexToTable('form_mapping', 'form_id');
		$this->addIndexToTable('form_mapping', 'is_deleted');
	}

	public function down() {
		// DOWN
		$this->removeIndexFromTable('form', 'is_deleted');
		
		$this->removeIndexFromTable('form_field', 'form_id');
		$this->removeIndexFromTable('form_field', 'ordering');
		$this->removeIndexFromTable('form_field', 'is_deleted');
		
		$this->removeIndexFromTable('form_value', 'form_instance_id');
		$this->removeIndexFromTable('form_value', 'form_field_id');
		$this->removeIndexFromTable('form_value', 'is_deleted');
		
		$this->removeIndexFromTable('form_field_metadata', 'form_field_id');
		$this->removeIndexFromTable('form_field_metadata', 'is_deleted');
		
		$this->removeIndexFromTable('form_instance', 'form_id');
		$this->removeIndexFromTable('form_instance', 'object_class');
		$this->removeIndexFromTable('form_instance', 'object_id');
		$this->removeIndexFromTable('form_instance', 'is_deleted');
		
		$this->removeIndexFromTable('form_mapping', 'form_id');
		$this->removeIndexFromTable('form_mapping', 'is_deleted');
	}

}
