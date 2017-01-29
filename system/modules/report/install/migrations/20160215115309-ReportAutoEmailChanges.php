<?php

class ReportAutoEmailChanges extends CmfiveMigration {

	public function up() {
		// UP
		if ($this->hasTable("report_member") && !$this->table("report_member")->hasColumn("is_email_recipient")) {
			$this->table("report_member")->addColumn("is_email_recipient", "boolean", ["default" => false])->save();
		}
		
		if ($this->hasTable("report_template") && !$this->table("report_template")->hasColumn("is_email_template")) {
			$this->table("report_template")->addColumn("is_email_template", "boolean", ["default" => false])->save();
		}
	}

	public function down() {
		// DOWN
		$this->hasTable("report_member") && $this->table("report_member")->hasColumn("is_email_recipient") ? $this->table("report_member")->removeColumn("is_email_recipient") : null;
		$this->hasTable("report_template") && $this->table("report_template")->hasColumn("is_email_template") ? $this->table("report_template")->removeColumn("is_email_template") : null;
	}

}
