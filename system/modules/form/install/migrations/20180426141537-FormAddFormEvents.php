<?php

class FormAddFormEvents extends CmfiveMigration {

	public function up() {
		// UP
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		if (!$this->hasTable('form_event')) {
			$this->table('form_event', [
				'id' => false,
				'primary_key' => 'id'
			])->addColumn($column)
				->addColumn('form_id', 'biginteger')
				->addColumn('title', 'string')
				->addColumn('description', 'string', ['null' => true])
				->addColumn('type', 'string', ['null' => true])
				->addColumn('is_active', 'boolean', ['default' => true])
				->addCmfiveParameters()
				->create();
		}

		/**
		 * CHANNEL PROCESSOR TABLE
		 */
		if (!$this->hasTable('form_event_processor')) {
			$this->table('form_event_processor', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('form_event_id', 'biginteger')
					->addColumn('class', 'string', ['limit' => 255])
					->addColumn('module', 'string', ['limit' => 255])
					->addColumn('name', 'string', ['limit' => 255, 'null' => true])
					->addColumn('processor_settings', 'string', ['limit' => 1024, 'null' => true])
					->addColumn('settings', 'string', ['limit' => 1024, 'null' => true])
					->addCmfiveParameters()
					->create();
		}
	}

	public function down() {
		// DOWN
		$this->hasTable('form_event') ? $this->dropTable("form_event") : null;
		$this->hasTable('form_event_processor') ? $this->dropTable("form_event_processor") : null;
	}

}
