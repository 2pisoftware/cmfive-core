<?php

class FormAddFormTemplate extends CmfiveMigration {

	public function up() {
		// UP
		if ($this->hasTable("form")) {
			$form = $this->table("form");
			
			if (!$form->hasColumn("header_template")) {
				$form->addColumn("header_template", 'text', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG]);
			}
			if (!$form->hasColumn("row_template")) {
				$form->addColumn("row_template", 'text', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG]);
			}
			if (!$form->hasColumn("summary_template")) {
				$form->addColumn("summary_template", 'text', ['limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG]);
			}
			
			$form->save();
		}
	}

	public function down() {
		// DOWN
		if ($this->hasTable("form")) {
			$form = $this->table("form");
			if ($form->hasColumn("header_template")) {
				$form->removeColumn("header_template");
			}
			if ($form->hasColumn("row_template")) {
				$form->removeColumn("row_template");
			}
			if ($form->hasColumn("summary_template")) {
				$form->removeColumn("summary_template");
			}
			$form->save();
		}
	}

}
