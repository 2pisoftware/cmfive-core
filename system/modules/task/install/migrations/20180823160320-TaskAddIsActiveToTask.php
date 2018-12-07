<?php

class TaskAddIsActiveToTask extends CmfiveMigration {

	public function up() {
		// UP
		$this->addColumnToTable('task', 'is_active', 'boolean', ['default' => true]);
	}

	public function down() {
		// DOWN
		$this->removeColumnFromTable('task', 'is_active');
	}

}
