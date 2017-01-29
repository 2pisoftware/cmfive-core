<?php

class FileNewAdapterMigration extends CmfiveMigration {
	
	public function up() {
		
		if ($this->hasTable("attachment")) {
			if (!$this->table("attachment")->hasColumn("adapter")) {
				$this->table("attachment")->addColumn("adapter", "string", ["default" => "local", "limit" => 255])->save();
			}
		}
		
	}
	
	public function down() {
		
		$this->hasTable("attachment") && $this->table("attachment")->hasColumn("adapter") ? $this->table("attachment")->removeColumn("adapter") : null;
		
	}
	
}


/**
 * SQL: ALTER TABLE `attachment` ADD `adapter` VARCHAR(255) NOT NULL DEFAULT 'local' ;
 */