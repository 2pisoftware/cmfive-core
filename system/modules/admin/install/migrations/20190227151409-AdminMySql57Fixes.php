<?php

/**
 * Up: Adds defaults to three columns in the migration table. This is a fix for MYSQL 5.7.
 * Down: There is no down becasue this should not be rolled back.
 *
 * @author Taliesin Millhouse <taliesin@2pisoftware.com>
 */
class AdminMySql57Fixes extends CmfiveMigration {

	public function up() {
		$this->changeColumnInTable('migration', 'pretext', 'string', ['null' => true, 'default' => null]);
		$this->changeColumnInTable('migration', 'posttext', 'string', ['null' => true, 'default' => null]);
		$this->changeColumnInTable('migration', 'description', 'string', ['null' => true, 'default' => null]);
	}
}
