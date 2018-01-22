<?php

/**
 * A patch migration to the amended initial migration
 */
class TaskAddPriorityAndTaskType extends CmfiveMigration {

	public function up() {
		// UP
		$this->addColumnToTable("task_group", "default_priority", "string",["null" => true]);
		$this->addColumnToTable('task_group', 'default_task_type', 'string',["null" => true]);
	}

	public function down() {
		// DOWN

	}

}
