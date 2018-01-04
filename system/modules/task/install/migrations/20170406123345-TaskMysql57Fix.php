<?php

class TaskMysql57Fix extends CmfiveMigration {

	public function up() {
		$this->changeColumnInTable('task', 'first_assignee_id', 'biginteger', ['null' => true]);
		$this->changeColumnInTable('task', 'dt_first_assigned', 'datetime', ['null' => true]);
		$this->changeColumnInTable('task', 'dt_assigned', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);
	}

	public function down() {

	}

}