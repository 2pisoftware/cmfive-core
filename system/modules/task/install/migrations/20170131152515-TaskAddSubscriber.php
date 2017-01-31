<?php

class TaskAddSubscriber extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		// Create task table
		if (!$this->hasTable("task_subscriber")) {
			$this->table('task_subscriber', [
					'id' => false,
					'primary_key' => 'id'
				])->addColumn($column)
				->addIdColumn("task_id")
				->addIdColumn("user_id")
				->addCmfiveParameters()
				->create();
		}
	}

	public function down() {
		$this->hasTable('task_subscriber') && $this->dropTable('task_subscriber');
	}
}