<?php

class MainInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		/**
		 * WIDGET CONFIG TABLE
		 */
		if (!$this->hasTable('widget_config')) {
			$this->table('widget_config', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('user_id', 'biginteger')
					->addColumn('destination_module', 'string', ['limit' => 255])
					->addColumn('source_module', 'string', ['limit' => 255])
					->addColumn('widget_name', 'string', ['limit' => 255])
					->addColumn('custom_config', 'text')
					->addCmfiveParameters()
					->create();
		}

		/**
		 * REST SESSION TABLE
		 */
		if (!$this->hasTable('rest_session')) {
			$this->table('rest_session', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('user_id', 'biginteger')
					->addColumn('token', 'string', ['limit' => 256])
					->addCmfiveParameters(['creator_id', 'modifier_id', 'is_deleted'])
					->create();
		}

		/**
		 * SESSIONS TABLE
		 */
		if (!$this->hasTable('sessions')) {
			$this->table('sessions', [
						'id' => false,
						'primary_key' => 'session_id'
					])->addColumn('session_id', 'string', ['limit' => 100])
					->addColumn('session_data', 'text')
					->addColumn('expires', 'integer', ['default' => 0])
					->create();
		}

		/**
		 * OBJECT HISTORY TABLE
		 */
		if (!$this->hasTable('object_history')) {
			$this->table('object_history', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('class_name', 'string', ['limit' => 255])
					->addColumn('object_id', 'biginteger')
					->create();
		}

		/**
		 * OBJECT HISTORY ENTRY TABLE
		 */
		if (!$this->hasTable('object_history_entry')) {
			$this->table('object_history_entry', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('history_id', 'biginteger')
					->addColumn('attr_name', 'string', ['limit' => 255, 'null' => true])
					->addColumn('attr_value', 'text', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG])
					->create();
		}

		/**
		 * OBJECT INDEX TABLE
		 */
		if (!$this->hasTable('object_index')) {
			$this->query('CREATE TABLE IF NOT EXISTS `object_index` (
				`id` bigint(20) NOT NULL AUTO_INCREMENT,
				`dt_created` datetime DEFAULT NULL,
				`dt_modified` datetime DEFAULT NULL,
				`creator_id` bigint(20) DEFAULT NULL,
				`modifier_id` bigint(20) DEFAULT NULL,
				`class_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
				`object_id` bigint(20) NOT NULL,
				`content` longtext COLLATE utf8_unicode_ci NOT NULL,
				PRIMARY KEY (`id`),
				FULLTEXT KEY `object_index_content` (`content`)
			  ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;');
		}

		/**
		 * OBJECT INDEX TABLE
		 */
		if (!$this->hasTable('object_modification')) {
			$this->table('object_modification', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
					->addColumn('table_name', 'string', ['limit' => 255])
					->addColumn('object_id', 'biginteger')
					->addCmfiveParameters(['is_deleted'])
					->create();
		}
//
//		$this->table('object_history', [
//					'id' => false,
//					'primary_key' => 'id'
//				])->addColumn($column)
//				->addColumn('table_name', 'string', ['limit' => 255])
//				->addColumn('object_id', 'biginteger')
//				->addCmfiveParameters('is_deleted')
//				->create();

	}

	public function down() {
		// DOWN
		$this->hasTable('widget_config') ? $this->dropTable('widget_config') : null;
		$this->hasTable('rest_session') ? $this->dropTable('rest_session') : null;
		$this->hasTable('sessions') ? $this->dropTable('sessions') : null;
		$this->hasTable('object_history') ? $this->dropTable('object_history') : null;
		$this->hasTable('object_modification') ? $this->dropTable('object_modification') : null;
		$this->hasTable('object_history_entry') ? $this->dropTable('object_history_entry') : null;
		$this->hasTable('object_index') ? $this->dropTable('object_index') : null;
	}

}
