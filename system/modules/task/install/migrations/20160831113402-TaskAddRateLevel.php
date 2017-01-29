<?php

class TaskAddRateLevel extends CmfiveMigration {

	public function up() {
		// UP
            $table = $this->table('task');
            $table->addDecimalColumn('rate',true)
                    ->save();
                
	}

	public function down() {
		// DOWN
            $this->removeColumnFromTable('task', 'rate');
	}

}
