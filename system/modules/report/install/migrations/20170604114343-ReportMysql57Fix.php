<?php

class ReportMysql57Fix extends CmfiveMigration {

	public function up() {

		// Report
		$this->changeColumnInTable('report', 'report_connection_id', 'biginteger', ['null' => true]);
		$this->changeColumnInTable('report', 'title', 'string', ['limit' => 255, 'null' => true]);
		
		// Report connection
		$this->changeColumnInTable('report_connection', 'db_driver', 'string', ['limit' => 255, 'null' => true]);

		// Report feed
		$this->changeColumnInTable('report_feed', 'report_id', 'biginteger', ['null' => true]);
		$this->changeColumnInTable('report_feed', 'title', 'string', ['limit' => 255, 'null' => true]);
		$this->changeColumnInTable('report_feed', 'report_key', 'string', ['limit' => 255, 'null' => true]);
		$this->changeColumnInTable('report_feed', 'url', 'string', ['limit' => 255, 'null' => true]);

		// Report member
		$this->changeColumnInTable('report_member', 'report_id', 'biginteger', ['null' => true]);
		$this->changeColumnInTable('report_member', 'user_id', 'biginteger', ['null' => true]);

		// Report template
		$this->changeColumnInTable('report_template', 'report_id', 'biginteger', ['null' => true]);
		$this->changeColumnInTable('report_template', 'template_id', 'biginteger', ['null' => true]);
	}

	public function down() {

	}

}