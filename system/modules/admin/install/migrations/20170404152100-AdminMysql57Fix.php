<?php

class AdminMysql57Fix extends CmfiveMigration {

	public function up() {

		// Update migration
		$this->changeColumnInTable('migration', 'path', 'string', ['limit' => 1024, 'null' => true]);
		$this->changeColumnInTable('migration', 'classname', 'string', ['limit' => 1024, 'null' => true]);
		$this->changeColumnInTable('migration', 'module', 'string', ['limit' => 1024, 'null' => true]);
		$this->changeColumnInTable('migration', 'batch', 'integer', ['default' => 0]);

		// Update audit
		$this->changeColumnInTable('audit', 'submodule', 'text',["null" => true]);
		$this->changeColumnInTable('audit', 'message', 'text',["null" => true]);
		$this->changeColumnInTable('audit', 'module', 'string', ['limit' => 128, 'null' => true]);
		$this->changeColumnInTable('audit', 'action', 'string', ['limit' => 128, 'null' => true]);
		$this->changeColumnInTable('audit', 'path', 'string', ['limit' => 1024, 'null' => true]);
		$this->changeColumnInTable('audit', 'ip', 'string', ['limit' => 128, 'null' => true]);
		$this->changeColumnInTable('audit', 'db_class', 'string', ['limit' => 128, "null" => true]);
		$this->changeColumnInTable('audit', 'db_action', 'string', ['limit' => 128, "null" => true]);

		// Update comment
		$this->changeColumnInTable('comment', 'comment', 'text', ['null' => true]);

		// Update lookup
		$this->changeColumnInTable('lookup', 'type', 'string', ['limit' => 255, 'null' => true]); 
		$this->changeColumnInTable('lookup', 'code', 'string', ['limit' => 255, 'null' => true]); 
		$this->changeColumnInTable('lookup', 'title', 'string', ['limit' => 255, 'null' => true]); 

		// Update printer
		$this->changeColumnInTable('printer', 'name', 'string', ['limit' => 512, 'null' => true]);
		$this->changeColumnInTable('printer', 'server', 'string', ['limit' => 512, 'null' => true]);

		// Update template
		$this->changeColumnInTable('template', 'title', 'string', ['limit' => 255, 'null' => true]);
		$this->changeColumnInTable('template', 'template_title', 'text', ['null' => true]);
		$this->changeColumnInTable('template', 'template_body', 'text', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG, 'null' => true]);
	}

	public function down() {

	}

}