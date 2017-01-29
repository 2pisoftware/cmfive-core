<?php

class TimelogInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
        $column->setName('id')
               ->setType('biginteger')
               ->setIdentity(true);

		if (!$this->hasTable("timelog")) {
			$this->table('timelog', [
				'id'          => false,
				'primary_key' => 'id'
			])->addColumn($column)
				->addColumn('user_id', 'biginteger')
				->addColumn('object_class', 'string', ['limit' => 255, 'null' => true])
				->addColumn('object_id', 'biginteger')
				->addColumn('dt_start', 'datetime')
				->addColumn('dt_end', 'datetime')
				->addColumn('time_type', 'string', ['limit' => 255, 'null' => true])
				->addColumn('is_suspect', 'boolean')
				->addCmfiveParameters()
				->create();
		}
	}

	public function down() {
		$this->hasTable("timelog") ? $this->dropTable("timelog") : null;
	}

}
