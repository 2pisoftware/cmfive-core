<?php

require "CmfiveTable.php";

use \Cmfive\Table as Table;
use Phinx\Db\Table\Column as Column;

class CmfiveMigration extends Phinx\Migration\AbstractMigration {
	
	public $w;
	
	public function setWeb($w) {
		$this->w = $w;
		return $this;
	}
	
	public function Column() {
		return new Column;
	}
	
	public function table($tableName, $options = array()) {
		return new Table($tableName, $options, $this->getAdapter());
	}
	
	public function tableWithId($tablename) {
		$id = $this->Column();
		$id->setName('id')
		->setType('biginteger')
		->setIdentity(true);
		
		return $this->table($tablename, [
			'id' => false,
			'primary_key' => 'id'
		])->addColumn($id);
	}
	
	// To preserve the table/data
	public function dropTable($tableName) {
		if ($this->hasTable($tableName)) {
			$table = $this->table($tableName);
			$table->rename(date('YmdHis') . $tableName);
		}

//		parent::dropTable($tableName);
	}
	
	/**
	 * Helper methods
	 */
	
	/**
	 * Adds a column to a table. Takes care of checking for table/column existance
	 * 
	 * @param string $table
	 * @param string $column
	 * @param string $datatype
	 * @param Array $options
	 * @return null
	 */
	public function addColumnToTable($table, $column, $datatype, $options = []) {
		if ($this->hasTable($table)) {
			if (!$this->table($table)->hasColumn($column)) {
				$this->table($table)->addColumn($column, $datatype, $options)->save();
			}
		}
	}
	
	/**
	 * Renames a column within a table
	 * 
	 * @param string $table
	 * @param string $column
	 * @param string $datatype
	 * @param Array $options
	 * @return null
	 */
	public function renameColumnInTable($table, $old_column, $new_column) {
		if ($this->hasTable($table)) {
			if ($this->table($table)->hasColumn($old_column)) {
				$this->table($table)->renameColumn($old_column, $new_column)->save();
			}
		}
	}
	
	/**
	 * Removes a column from a table. Takes care of checking for table/column
	 * existance
	 * 
	 * @param string $table
	 * @param string $column
	 * @return null
	 */
	public function removeColumnFromTable($table, $column) {
		if ($this->hasTable($table)) {
			if ($this->table($table)->hasColumn($column)) {
				$this->table($table)->removeColumn($column);
			}
		}
	}
	
	/**
	 * Helper function to update data for a given table row, an "id" field in
	 * the $data array is expected otherwise no updates will occur
	 * 
	 * @param string $table
	 * @param array $data
	 * @return null
	 */
	public function updateRowInTable($table, Array $data) {
		if (empty($data['id']) || empty($data)) {
			return;
		}
		
		// Validate table
		if ($this->hasTable($table)) {
			$update_statement_string = "UPDATE {$table} SET ";
			foreach($data as $column_name => $column_value) {
				if ($column_name == "id" || is_int($column_name)) {
					continue;
				}
				
				$update_statement_string .= " {$column_name}=\"{$column_value}\",";
			}
			
			$update_statement_string = rtrim($update_statement_string, ',');
			
			$update_statement_string .= " WHERE id=" . $data['id'];
			
			$this->execute($update_statement_string);
		}
	}

	/**
	 * Removes a column from a table. Takes care of checking for table/column
	 * existance
	 * 
	 * @param string $table
	 * @param string $column
	 * @param string $type
	 * @param Array $options
	 * @return null
	 */
	public function changeColumnInTable($table, $column, $type, $options = []) {
		if ($this->hasTable($table)) {
			if ($this->table($table)->hasColumn($column)) {
				$this->table($table)->changeColumn($column, $type, $options)->save();
			}
		}
	}
	
	/**
	 * Checks if the table and column exists and applies an index to that given 
	 * column if it does.
	 * 
	 * @param string $table
	 * @param string $column
	 * @return null
	 */
	public function addIndexToTable($table, $column) {
		if ($this->hasTable($table)) {
			if ($this->table($table)->hasColumn($column)) {
				$this->table($table)->addIndex($column)->update();
			}
		}
	}
	
	/**
	 * Checks if the table and column exists and removes an index from that 
	 * given column if it does.
	 * 
	 * @param string $table
	 * @param string $column
	 * @return null
	 */
	public function removeIndexFromTable($table, $column) {
		if ($this->hasTable($table)) {
			if ($this->table($table)->hasColumn($column)) {
				$this->table($table)->removeIndex($column)->update();
			}
		}
	}
}

class MigrationException extends Exception {
	
}