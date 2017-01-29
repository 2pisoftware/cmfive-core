<?php

use \Phinx\Db\Adapter\MysqlAdapter;

class AdminInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		/**
		 * MIGRATION TABLE
		 */
		if (!$this->hasTable('migration')) {
			$this->table('migration', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('path', 'string', ['limit' => 1024])
					->addColumn('classname', 'string', ['limit' => 1024])
					->addColumn('module', 'string', ['limit' => 1024])
					->addColumn("batch", "integer")
					->addCmfiveParameters(['dt_modified', 'modifier_id', 'is_deleted'])
					->create();
		}
		
		/**
		 * AUDIT TABLE
		 */
		if (!$this->hasTable('audit')) {
			$this->table('audit', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('submodule', 'text',["null"=>true])
					->addColumn('message', 'text',["null"=>true])
					->addColumn('module', 'string', ['limit' => 128])
					->addColumn('action', 'string', ['limit' => 128])
					->addColumn('path', 'string', ['limit' => 1024])
					->addColumn('ip', 'string', ['limit' => 128])
					->addColumn('db_class', 'string', ['limit' => 128,"null"=>true])
					->addColumn('db_action', 'string', ['limit' => 128,"null"=>true])
					->addColumn('db_id', 'biginteger',["null"=>true])
					->addCmfiveParameters(['dt_modified', 'modifier_id', 'is_deleted'])
					->create();
		}
		
		/**
		 * COMMENT TABLE
		 */
		if (!$this->hasTable('comment')) {
			$this->table('comment', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('obj_table', 'string', ['limit' => 200])
					->addColumn('obj_id', 'biginteger', ['null' => true])
					->addColumn('comment', 'text')
					->addColumn('is_internal', 'boolean', ['default' => 0])
					->addColumn('is_system', 'boolean', ['default' => 0])
					->addCmfiveParameters()
					->create();
		}
		
		/**
		 * LOOKUP TABLE
		 */
		if (!$this->hasTable('lookup')) {
			$this->table('lookup', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('weight', 'integer', ['limit' => 11,'null' => true])
					->addColumn('type', 'string', ['limit' => 255])
					->addColumn('code', 'string', ['limit' => 255])
					->addColumn('title', 'string', ['limit' => 255])
					->addCmfiveParameters(['dt_created', 'creator_id', 'dt_modified', 'modifier_id'])
					->create();
		}
		
		/**
		 * PRINTER TABLE
		 */
		if (!$this->hasTable('printer')) {
			$this->table('printer', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('name', 'string', ['limit' => 512])
					->addColumn('server', 'string', ['limit' => 512])
					->addColumn('port', 'string', ['limit' => 256,'null' => true])
					->create();
		}
		
		/**
		 * TEMPLATE TABLE
		 */
		if (!$this->hasTable('template')) {
			$this->table('template', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('title', 'string', ['limit' => 255])
					->addColumn('description', 'string', ['limit' => 255, 'null' => true])
					->addColumn('category', 'string', ['limit' => 255, 'null' => true])
					->addColumn('module', 'string', ['limit' => 255, 'null' => true])
					->addColumn('template_title', 'text')
					->addColumn('template_body', 'text', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG])
					->addColumn('test_title_json', 'text',['null' => true])
					->addColumn('test_body_json', 'text',['null' => true])
					->addColumn('is_active', 'boolean', ['default' => 1])
					->addCmfiveParameters()				
					->create();
		}
	}

	public function down() {
		$this->hasTable('migration') ? $this->dropTable('migration') : null;
		$this->hasTable('audit') ? $this->dropTable('audit') : null;
		$this->hasTable('comment') ? $this->dropTable('comment') : null;
		$this->hasTable('lookup') ? $this->dropTable('lookup') : null;
		$this->hasTable('printer') ? $this->dropTable('printer') : null;
		$this->hasTable('template') ? $this->dropTable('template') : null;
	}

}
