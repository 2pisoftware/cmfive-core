<?php

class FormMysql57Updates extends CmfiveMigration {

	public function up() {
		// UP
		$this->table('form_application')->changeColumn('title', 'string', ['null' => true, 'default' => null]);
        $this->table('form')->changeColumn('header_template', 'text', ['null' => true, 'default' => null, 'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG]);
        $this->table('form')->changeColumn('row_template', 'text', ['null' => true, 'default' => null, 'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG]);
        $this->table('form')->changeColumn('summary_template', 'text', ['null' => true, 'default' => null, 'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG]);

		$this->table('form_field_metadata')->changeColumn('form_field_id', 'biginteger', ['null' => true, 'default' => null]);
	}

	public function down() {
		// DOWN
		
	}

}
