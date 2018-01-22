<?php

class TaskAddEffort extends CmfiveMigration {

	public function up() {
		// UP
        if ($this->hasTable("task") && !$this->table("task")->hasColumn("effort")) {
            $this->table("task")->addColumn("effort", "string", ["limit" => 255, "null" => true])->save();
        }
	}

	public function down() {
		// DOWN
        $this->hasTable("task") && $this->table("task")->hasColumn("effort") ? $this->table("task")->removeColumn("effort") : null;
	}

}
