<?php

class FormRemoveValueMaskAndType extends CmfiveMigration {

	public function up() {
		// UP
		$this->removeColumnFromTable('form_value', 'mask');
		$this->removeColumnFromTable('form_value', 'field_type');
	}

	public function down() {
		// DOWN
		$this->addColumnToTable('form_value', "field_type", "string", ["limit" => 256]);
		$this->addColumnToTable('form_value', "mask", "string", ["limit" => 1024, "null" => true]);
	}

}
