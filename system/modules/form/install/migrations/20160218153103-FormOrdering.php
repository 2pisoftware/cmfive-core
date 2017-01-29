<?php

class FormOrdering extends CmfiveMigration {

	public function up() {
		// UP
		if ($this->hasTable("form_field") && !$this->table("form_field")->hasColumn("ordering")) {
			$this->table("form_field")->addColumn("ordering", "integer", ["null" => true])->save();
		}
	}

	public function down() {
		// DOWN
		$this->hasTable("form_field") && $this->table("form_field")->hasColumn("ordering") ? $this->table("form_field")->removeColumn("ordering") : null;
	}

}
