<?php

class TaskChecklistInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		// Create task checklist table
		if (!$this->hasTable("task_checklist")) {
			$this->table('task_checklist', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('title', 'string', ['limit' => 255, 'null' => true])
				->addColumn('is_template', 'boolean', ['default' => 0])
				->addCmfiveParameters()
				->create();
		}
		
		// Create task checklist item table
		if(!$this->hasTable("task_checklist_item")) {
			$this->table('task_checklist_item', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('checklist_id', 'biginteger')
				->addColumn('title', 'string', ['limit' => 255, 'null' => true])
				->addCmfiveParameters()
				->create();
		}

		// Create taskgroup table
		if (!$this->hasTable("task_checklist_mapping")) {
			$this->table('task_checklist_mapping', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('checklist_id', 'biginteger')
				->addColumn('task_id', 'biginteger')
                ->addCmfiveParameters()
				->create();
		}
		
		// Create taskgroup member table
		if (!$this->hasTable("task_checklist_item_mapping")) {
			$this->table('task_checklist_item_mapping', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addColumn('checklist_id', 'biginteger')
				->addColumn('task_id', 'biginteger')
                ->addColumn('is_checked', 'boolean', ['default' => 0])
                ->addCmfiveParameters()
				->create();
		}
    }

	public function down() {
		$this->hasTable("task") ? $this->dropTable("task_checklist") : null;
		$this->hasTable("task_data") ? $this->dropTable("task_checklist_item") : null;
		$this->hasTable("task_group") ? $this->dropTable("task_checklist_mapping") : null;
		$this->hasTable("task_group_member") ? $this->dropTable("task_checklist_item_mapping") : null;
	}

}
