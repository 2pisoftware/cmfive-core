<?php

class ReportIndexingUpdate extends CmfiveMigration {

	public function up() {
		// UP
		$this->addIndexToTable('report', 'report_connection_id');
		$this->addIndexToTable('report', 'is_approved');
		$this->addIndexToTable('report', 'is_deleted');
		
		$this->addIndexToTable('report_connection', 'is_deleted');
		
		$this->addIndexToTable('report_feed', 'report_id');
		$this->addIndexToTable('report_feed', 'is_deleted');
		
		$this->addIndexToTable('report_member', 'report_id');
		$this->addIndexToTable('report_member', 'user_id');
		
		$this->addIndexToTable('report_template', 'report_id');
		$this->addIndexToTable('report_template', 'template_id');
		$this->addIndexToTable('report_template', 'is_deleted');
	}

	public function down() {
		// DOWN
		$this->removeIndexFromTable('report', 'report_connection_id');
		$this->removeIndexFromTable('report', 'is_approved');
		$this->removeIndexFromTable('report', 'is_deleted');
		
		$this->removeIndexFromTable('report_connection', 'is_deleted');
		
		$this->removeIndexFromTable('report_feed', 'report_id');
		$this->removeIndexFromTable('report_feed', 'is_deleted');
		
		$this->removeIndexFromTable('report_member', 'report_id');
		$this->removeIndexFromTable('report_member', 'user_id');
		
		$this->removeIndexFromTable('report_template', 'report_id');
		$this->removeIndexFromTable('report_template', 'template_id');
		$this->removeIndexFromTable('report_template', 'is_deleted');
	}

}
