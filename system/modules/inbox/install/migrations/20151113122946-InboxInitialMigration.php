<?php

class InboxInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		/**
		 * INBOX TABLE
		 */
		if (!$this->hasTable('inbox')) {
			$this->table('inbox', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('user_id', 'biginteger')
					->addColumn('sender_id', 'biginteger', ['null' => true])
					->addColumn('subject', 'string', ['limit' => 255,'null' => true])
					->addColumn('message_id', 'biginteger', ['null' => true])
					->addColumn('dt_read', 'datetime', ['null' => true])
					->addColumn('is_new', 'boolean', ['default' => 1])
					->addColumn('dt_archived', 'datetime', ['null' => true])
					->addColumn('is_archived', 'boolean', ['default' => 1])
					->addColumn('parent_message_id', 'biginteger', ['null' => true])
					->addColumn('has_parent', 'boolean', ['default' => 0])
					->addColumn('del_forever', 'boolean', ['default' => 0])
					->addCmfiveParameters(['creator_id', 'modifier_id', 'dt_modified'])
					->create();
		}

		/**
		 * INBOX MESSAGE TABLE
		 */
		if (!$this->hasTable('inbox_message')) {
			$this->table('inbox_message', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('message', 'text', ['null' => true])
					->addColumn('digest', 'string', ['limit' => 255,'null' => true])
					->create();
		}
	}

	public function down() {
		$this->hasTable('inbox') ? $this->dropTable('inbox') : null;
		$this->hasTable('inbox_messages') ? $this->dropTable('inbox_message') : null;
	}

}
