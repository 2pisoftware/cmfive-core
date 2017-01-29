<?php

class FileInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		/**
		 * ATTACHMENT TABLE
		 */
		if (!$this->hasTable('attachment')) {
			$this->table('attachment', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('parent_table', 'string', ['limit' => 255])
					->addColumn('obj_id', 'biginteger')
					->addColumn('modifier_user_id', 'biginteger', ['null' => true])
					->addColumn('filename', 'string', ['limit' => 255])
					->addColumn('mimetype', 'string', ['limit' => 255, 'null' => true])
					->addColumn('title', 'string', ['limit' => 255, 'null' => true])
					->addColumn('description', 'text', ['null' => true])
					->addColumn('fullpath', 'text')
					->addColumn('type_code', 'string', ['limit' => 255, 'null' => true])
					->addCmfiveParameters()
					->create();
		}
		
		/**
		 * ATTACHMENT TYPE TABLE
		 */
		if (!$this->hasTable('attachment_type')) {
			$this->table('attachment_type', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('table_name', 'string', ['limit' => 255])
					->addColumn('code', 'string', ['limit' => 255, 'null' => true])
					->addColumn('title', 'string', ['limit' => 255])
					->addColumn('is_active', 'boolean', ['default' => 1])
					->create();
		}
	}

	public function down() {
		$this->hasTable('attachment') ? $this->dropTable('attachment') : null;
		$this->hasTable('attachment_type') ? $this->dropTable('attachment_type') : null;
	}

}
