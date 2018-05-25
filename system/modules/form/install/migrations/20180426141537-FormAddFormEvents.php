<?php

class FormAddFormEvents extends CmfiveMigration {

	public function up() {
		// UP
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		

		/**
		 * CHANNEL PROCESSOR TABLE
		 */
		if (!$this->hasTable('form_event')) {
			$this->table('form_event', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('title', 'string', ['limit' => 255, 'null' => true])
					->addColumn('form_id', 'biginteger')
					->addColumn('form_application_id', 'biginteger')
					->addColumn('event_type', 'string', ['null' => true])
					->addColumn('is_active', 'boolean', ['default' => true])
					->addColumn('class', 'string', ['limit' => 255])
					->addColumn('module', 'string', ['limit' => 255])
					->addColumn('processor_settings', 'string', ['limit' => 1024, 'null' => true])
					->addColumn('settings', 'string', ['limit' => 1024, 'null' => true])
					->addCmfiveParameters()
					->create();
		}
	}

	public function down() {
		// DOWN
		$this->hasTable('form_event') ? $this->dropTable("form_event") : null;
		
	}

}
