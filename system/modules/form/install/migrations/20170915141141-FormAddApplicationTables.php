<?php

class FormAddApplicationTables extends CmfiveMigration {

	public function up() {
		// UP
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		if (!$this->hasTable('form_application_mapping')) {
			$this->table('form_application_mapping', [
				'id' => false,
				'primary_key' => 'id'
			])->addColumn($column)
				->addColumn('form_id', 'biginteger')
				->addColumn('application_id', 'biginteger')
				->addCmfiveParameters()
				->create();
		}

		if (!$this->hasTable('form_application')) {
			$this->table('form_application', [
				'id' => false,
				'primary_key' => 'id'
			])->addColumn($column)
				->addColumn('title', 'string')
				->addColumn('description', 'string', ['null' => true])
				->addColumn('is_active', 'boolean', ['default' => true])
				->addCmfiveParameters()
				->create();
		}

		if (!$this->hasTable('form_application_member')) {
			$this->table('form_application_member', [
				'id' => false,
				'primary_key' => 'id'
			])->addColumn($column)
				->addColumn('application_id', 'biginteger')
				->addColumn('member_user_id', 'biginteger')
				->addColumn('role', 'string', ['default' => 'VIEWER'])
				->addCmfiveParameters()
				->create();
		}

		if (!$this->hasTable('form_application_view')) {
			$this->table('form_application_view', [
				'id' => false,
				'primary_key' => 'id'
			])->addColumn($column)
				->addColumn('application_id', 'biginteger')
				->addColumn('form_id', 'biginteger', ['null' => true])
				->addColumn('title', 'string')
				->addColumn('description', 'string', ['null' => true])
				->addColumn('template_id', 'biginteger', ['null' => true])
				->addCmfiveParameters()
				->create();
		}
	}

	public function down() {
		// DOWN
		$this->dropTable('form_application');
		$this->dropTable('form_application_member');
		$this->dropTable('form_application_mapping');
		$this->dropTable('form_application_view');
	}

}
