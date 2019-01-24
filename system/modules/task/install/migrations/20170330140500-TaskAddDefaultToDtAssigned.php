<?php

class TaskAddDefaultToDtAssigned extends CmfiveMigration {

	// A newer migration adds a default => null to the dt_assigned field so this migration is incorrect when we set the
	// assignee to "unassigned".
	public function up() {
		// if ($this->hasTable('task') && $this->table('task')->hasColumn('dt_assigned')) {
			// $this->table('task')->changeColumn('dt_assigned', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])->save();
		// }
	}

	public function down() {

	}

}