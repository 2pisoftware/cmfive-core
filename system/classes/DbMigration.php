<?php
require_once "DbTable.php";

class DbMigration extends DbService {
	
	/**
	 * Returns a DbTable object primed for creation of a new database table
	 * 
	 * @param string $name
	 * @param boolean $createId
	 */
	function createTable($name,$createId = true) {
		
	}
		
	/**
	 * Returns a DbTable object primed for modifying an existing database table
	 * 
	 * @param string $name
	 */
	function modifyTable($name) {
		
	}
	
	/**
	 * Rename a database table
	 * 
	 * @param string $oldname
	 * @param string $newname
	 */
	function renameTable($oldname, $newname) {
		
	}
	
	/**
	 * Returns a table object primed for deleting all data
	 * 
	 * @param string $name
	 */
	function deleteTable($name) {
		
	}
	
	/**
	 * Returns a table object primed for dropping structure and data
	 * 
	 * @param string $name
	 */
	function dropTable($name) {
	
	}
	
	function typeBigInt() {
		return "BIGINT";
	}

	function typeId() {
		return $this->BigInt();
	}
	
	function typeVarchar(int $length) {
		return "VARCHAR(".$length.")";
	}
	
	function typeText() {
		return "TEXT";
	}
	
	function typeLongText() {
		return "LONGTEXT";
	}
	
}