<?php

class AuthInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		/**
		 * USER TABLE
		 */
		if (!$this->hasTable('user')) {
			$this->table('user', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('login', 'string', ['limit' => 32])
					->addColumn('password', 'string', ['limit' => 255, 'null' => true])
					->addColumn('password_salt', 'string', ['limit' => 255, 'null' => true])
					->addColumn('contact_id', 'biginteger', ['null' => true])
					->addColumn('password_reset_token', 'string', ['limit' => 40, 'null' => true])
					->addColumn('dt_password_reset_at', 'timestamp', ['null' => true])
					->addColumn('redirect_url', 'string', ['limit' => 255, 'default' => 'main/index'])
					->addColumn('is_admin', 'boolean', ['default' => 0])
					->addColumn('is_active', 'boolean', ['default' => 1])
					->addColumn('is_group', 'boolean', ['default' => 0])
					->addColumn('dt_lastlogin', 'datetime', ['null' => true])
					->addCmfiveParameters(['dt_modified', 'modifier_id', 'creator_id'])
					->create();
		}
		
		/**
		 * USER ROLE TABLE
		 */
		if (!$this->hasTable('user_role')) {
			$this->table('user_role', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('user_id', 'biginteger')
					->addColumn('role', 'string', ['limit' => 255])
					->addIndex(['user_id', 'role'], ['unique' => true])
					->create();
		}
		
		/**
		 * GROUP USER TABLE
		 */
		if (!$this->hasTable('group_user')) {
			$this->table('group_user', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('group_id', 'biginteger')
					->addColumn('user_id', 'biginteger')
					->addColumn('role', 'string', ['limit' => 32])
					->addColumn('is_active', 'boolean')
					->addCmfiveParameters(['creator_id', 'dt_modified', 'modifier_id', 'is_deleted'])
					->create();
		}
		
		/**
		 * CONTACT
		 */
		if (!$this->hasTable('contact')) {
			$this->table('contact', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('firstname', 'string', ['limit' => 128])
					->addColumn('lastname', 'string', ['limit' => 255])
					->addColumn('othername', 'string', ['limit' => 255, 'null' => true])
					->addColumn('title', 'string', ['limit' => 32, 'null' => true])
					->addColumn('homephone', 'string', ['limit' => 64, 'null' => true])
					->addColumn('workphone', 'string', ['limit' => 64, 'null' => true])
					->addColumn('mobile', 'string', ['limit' => 64, 'null' => true])
					->addColumn('priv_mobile', 'string', ['limit' => 64, 'null' => true])
					->addColumn('fax', 'string', ['limit' => 64, 'null' => true])
					->addColumn('email', 'string', ['limit' => 255, 'null' => true])
					->addColumn('notes', 'text', ['null' => true])
					->addColumn('private_to_user_id', 'biginteger', ['null' => true])
					->addCmfiveParameters([	'modifier_id'])
					->create();
		}
	}

	public function down() {
		$this->hasTable('user') ? $this->dropTable('user') : null;
		$this->hasTable('user_role') ? $this->dropTable('user_role') : null;
		$this->hasTable('group_user') ? $this->dropTable('group_user') : null;
		$this->hasTable('contact') ? $this->dropTable('contact') : null;
	}

}
