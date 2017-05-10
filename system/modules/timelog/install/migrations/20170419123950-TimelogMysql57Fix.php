<?php

class TimelogMysql57Fix extends CmfiveMigration {

	public function up() {
		// UP
		$this->changeColumnInTable('timelog', 'dt_end', 'datetime', ['null' => true]);
        $this->changeColumnInTable('timelog', 'is_suspect', 'boolean', ['default' => 0]);
	}

	public function down() {
		// DOWN
	}

}
