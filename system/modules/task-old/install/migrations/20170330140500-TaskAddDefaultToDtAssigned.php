<?php

class TaskAddDefaultToDtAssigned extends CmfiveMigration {

	public function up() {
		if ($this->hasTable('task') && $this->table('task')->hasColumn('dt_assigned')) {
			$this->table('task')->changeColumn('dt_assigned', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])->save();
		}
	}

	public function down() {

	}

}