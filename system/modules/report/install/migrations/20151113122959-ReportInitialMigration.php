<?php

class ReportInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		// Create report table
		if (!$this->hasTable("report")) {
			$this->table('report', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('report_connection_id', 'biginteger')
				->addColumn('title', 'string', ['limit' => 255])
				->addColumn('module', 'string', ['limit' => 255, 'null' => true])
				->addColumn('category', 'string', ['limit' => 255, 'null' => true])
				->addColumn('report_code', 'text', ['null' => true])
				->addColumn('is_approved', 'boolean',["default"=>1])
				->addColumn('description', 'text', ['null' => true])
				->addColumn('sqltype', 'string', ['limit' => 255, 'null' => true])
				->addCmfiveParameters(['dt_created', 'dt_modified', 'creator_id', 'modifier_id'])
				->create();
		}
		
		// Create report connection table
		if (!$this->hasTable("report_connection")) {
			$this->table('report_connection', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('db_driver', 'string', ['limit' => 255])
				->addColumn('db_host', 'string', ['limit' => 255, 'null' => true])
				->addColumn('db_port', 'string', ['limit' => 255, 'null' => true])
				->addColumn('db_database', 'string', ['limit' => 255, 'null' => true])
				->addColumn('db_file', 'string', ['limit' => 255, 'null' => true])
				->addColumn('s_db_user', 'string', ['limit' => 1024, 'null' => true])
				->addColumn('s_db_password', 'string', ['limit' => 1024, 'null' => true])
				->addCmfiveParameters()
				->create();
		}
		
		// Create report feed table
		if (!$this->hasTable("report_feed")) {
			$this->table('report_feed', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('report_id', 'biginteger')
				->addColumn('title', 'string', ['limit' => 255])
				->addColumn('description', 'text', ['null' => true])
				->addColumn('report_key', 'string', ['limit' => 255])
				->addColumn('url', 'string', ['limit' => 1024])
				->addCmfiveParameters(['dt_modified', 'creator_id', 'modifier_id'])
				->create();
		}
		
		// Create report feed table
		if (!$this->hasTable("report_member")) {
			$this->table('report_member', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('report_id', 'biginteger')
				->addColumn('user_id', 'biginteger')
				->addColumn('role', 'string', ['limit' => 255, 'null' => true])
				->addCmfiveParameters(['dt_created', 'dt_modified', 'creator_id', 'modifier_id'])
				->create();
		}
		
		// Create report feed table
		if (!$this->hasTable("report_template")) {
			$this->table('report_template', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('report_id', 'biginteger')
				->addColumn('template_id', 'biginteger')
				->addColumn('type', 'string', ['limit' => 255, 'null' => true])
				->addCmfiveParameters()
				->create();
		}
	}

	public function down() {
		$this->hasTable("report") ? $this->dropTable('report') : null;
		$this->hasTable("report_connection") ? $this->dropTable('report_connection') : null;
		$this->hasTable("report_feed") ? $this->dropTable('report_feed') : null;
		$this->hasTable("report_member") ? $this->dropTable('report_member') : null;
		$this->hasTable("report_template") ? $this->dropTable('report_template') : null;
	}

}
