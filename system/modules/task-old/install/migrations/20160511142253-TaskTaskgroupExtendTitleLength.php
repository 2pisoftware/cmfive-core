<?php

class TaskTaskgroupExtendTitleLength extends CmfiveMigration {

	public function up() {
		// UP
		if ($this->hasTable("task_group") && $this->table("task_group")->hasColumn("title")) {
			$this->table("task_group")->changeColumn("title", "string", ["limit" => 255])->save();
		}
	}

	public function down() {
		// DOWN
		if ($this->hasTable("task_group") && $this->table("task_group")->hasColumn("title")) {
			$this->table("task_group")->changeColumn("title", "string", ["limit" => 50])->save();
		}
	}

}
