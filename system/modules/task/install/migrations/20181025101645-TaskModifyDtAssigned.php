<?php

class TaskModifyDtAssigned extends CmfiveMigration {

	public function up() {
		// UP
		$this->changeColumnInTable('task', 'dt_assigned', 'datetime', ['null' => true]);
	}

	public function down() {
		// DOWN
	}

}
