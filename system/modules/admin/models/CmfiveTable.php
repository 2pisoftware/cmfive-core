<?php

namespace Cmfive;

class Table extends \Phinx\Db\Table {

	public function addCmfiveParameters($exclude = []) {
		// dt_created
		if (!in_array("dt_created", $exclude)) {
			$this->addDatetimeColumn("dt_created");
		}

		// dt_modified
		if (!in_array("dt_modified", $exclude)) {
			$this->addDateTimeColumn("dt_modified");
		}

		// creator_id
		if (!in_array("creator_id", $exclude)) {
			$this->addIdColumn("creator_id");
		}

		// modifier_id
		if (!in_array("modifier_id", $exclude)) {
			$this->addIdColumn("modifier_id");
		}

		// is_deleted
		if (!in_array("is_deleted", $exclude)) {
			$this->addBooleanColumn("is_deleted", false, 0);
			$this->addIndex('is_deleted');
		}

		return $this;
	}

	public function addDecimalColumn($name, $null = true, $precision = 20, $scale = 2) {
		return $this->addColumn($name, 'decimal', ['precision' => $precision, 'scale' => $scale, 'null' => $null]);
	}

	public function addMoneyColumn($name, $null = true) {
		return $this->addDecimalColumn($name, $null, 20, 2);
	}

	public function addBigIntegerColumn($name, $null = true) {
		return $this->addColumn($name, 'biginteger', ['null' => $null]);
	}

	public function addIdColumn($name, $null = true) {
		return $this->addColumn($name, 'biginteger', ['null' => $null]);
	}

	public function addIntegerColumn($name, $null = true, $default = null) {
		return $this->addColumn($name, 'integer', ['null' => $null, 'default' => $default]);
	}

	public function addStringColumn($name, $null = true, $limit = 255, $default = null) {
		return $this->addColumn($name, 'string', ['null' => $null, 'limit' => $limit, 'default' => $default]);
	}

	public function addTextColumn($name, $null = true, $limit = 1024, $default = null) {
		return $this->addColumn($name, 'text', ['null' => $null, 'limit' => $limit, 'default' => $default]);
	}

	public function addDateColumn($name, $null = true) {
		if (!startsWith($name, "d_")) {
			throw new \MigrationException("Cmfive mandates to prefix date fields with 'd_'.");
		}
		return $this->addColumn($name, 'date', ['null' => $null]);
	}

	public function addDateTimeColumn($name, $null = true) {
		if (!startsWith($name, "dt_")) {
			throw new \MigrationException("Cmfive mandates to prefix datetime fields with 'dt_'.");
		}
		return $this->addColumn($name, 'datetime', ['null' => $null]);
	}

	public function addTimeColumn($name, $null = true) {
		if (!startsWith($name, "t_")) {
			throw new \MigrationException("Cmfive mandates to prefix time fields with 't_'.");
		}
		return $this->addColumn($name, 'time', ['null' => $null]);
	}

	public function addBooleanColumn($name, $null = true, $default = false, $prefixOverride = false) {
		if (!$prefixOverride && !(startsWith($name, "is_") || startsWith($name, "has_"))) {
			throw new \MigrationException("Cmfive mandates to prefix boolean fields with either 'is_' or 'has_'.");
		}

		return $this->addColumn($name, 'boolean', ['null' => $null, 'default' => $default]);
	}

}
