<?php

class FormInitialMigration extends CmfiveMigration {

	public function up() {
		// UP
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		if (!$this->hasTable('form')) {
			$this->table('form', [
				'id' => false,
				'primary_key' => 'id'
			])->addColumn($column)
					->addColumn("title", "string", ["limit" => 256])
					->addColumn("description", "string", ["limit" => 1024,'null' => true])
					->addCmfiveParameters()
					->create();
		}
		
		if (!$this->hasTable('form_field')) {
			$this->table('form_field', [
				'id' => false,
				'primary_key' => 'id'
			])->addColumn($column)
					->addColumn("form_id", "biginteger")
					->addColumn("name", "string", ["limit" => 256])
					->addColumn("technical_name", "string", ["limit" => 256,'null' => true])
					->addColumn("interface_class", "string", ["limit" => 256, "null" => true])
					->addColumn("type", "string", ["limit" => 256])
					->addColumn("mask", "string", ["limit" => 1024, "null" => true])
					->addCmfiveParameters()
					->create();
		}
		
		if (!$this->hasTable('form_value')) {
			$this->table('form_value', [
				'id' => false,
				'primary_key' => 'id'
			])->addColumn($column)
					->addColumn("form_instance_id", "biginteger")
					->addColumn("form_field_id", "biginteger")
					->addColumn("value", "string", ["limit" => 1024, "null" => true])
					->addColumn("field_type", "string", ["limit" => 256])
					->addColumn("mask", "string", ["limit" => 1024, "null" => true])
					->addCmfiveParameters()
					->create();
		}
		
		if (!$this->hasTable('form_field_metadata')) {
			$this->table('form_field_metadata', [
				'id' => false,
				'primary_key' => 'id'
			])->addColumn($column)
					->addColumn("form_field_id", "biginteger")
					->addColumn("meta_key", "string", ["limit" => 256])
					->addColumn("meta_value", "string", ["limit" => 256, "null" => true])
					->addCmfiveParameters()
					->create();
		}
		
		if (!$this->hasTable('form_instance')) {
			$this->table('form_instance', [
				'id' => false,
				'primary_key' => 'id'
			])->addColumn($column)
					->addColumn("form_id", "biginteger")
					->addColumn("object_class", "string", ["limit" => 256])
					->addColumn("object_id", "biginteger")
					->addCmfiveParameters()
					->create();
		}
		
		if (!$this->hasTable('form_mapping')) {
			$this->table('form_mapping', [
				'id' => false,
				'primary_key' => 'id'
			])->addColumn($column)
					->addColumn("form_id", "biginteger")
					->addColumn("object", "string", ["limit" => 256])
					->addCmfiveParameters()
					->create();
		}
	}

	public function down() {
		// DOWN
		$this->hasTable("form") ? $this->dropTable("form") : null;
		$this->hasTable("form_field") ? $this->dropTable("form_field") : null;
		$this->hasTable("form_value") ? $this->dropTable("form_value") : null;
		$this->hasTable("form_field_metadata") ? $this->dropTable("form_field_metadata") : null;
		$this->hasTable("form_instance") ? $this->dropTable("form_instance") : null;
		$this->hasTable("form_mapping") ? $this->dropTable("form_mapping") : null;
	}

}
