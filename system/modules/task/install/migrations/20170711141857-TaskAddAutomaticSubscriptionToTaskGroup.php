<?php

class TaskAddAutomaticSubscriptionToTaskGroup extends CmfiveMigration {

	public function up() {
		// UP
		$this->addColumnToTable('task_group', 'is_automatic_subscription', 'boolean', ['default' => false]);
	}

	public function down() {
		// DOWN
		$this->removeColumnFromTable('task_group', 'is_automatic_subscription');
	}

}
