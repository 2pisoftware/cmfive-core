<?php

class TaskAddRateLevel extends CmfiveMigration {

	public function up() {
		// UP
			$this->addColumnToTable('task','rate','decimal',["null"=>true]);
           
                
	}

	public function down() {
		// DOWN
            $this->removeColumnFromTable('task', 'rate');
	}

}
