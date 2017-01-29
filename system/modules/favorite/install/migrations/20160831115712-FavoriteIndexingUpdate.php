<?php

class FavoriteIndexingUpdate extends CmfiveMigration {

	public function up() {
		// UP
		$this->addIndexToTable('favorite', 'object_id');
		$this->addIndexToTable('favorite', 'user_id');
		$this->addIndexToTable('favorite', 'is_deleted');
	}

	public function down() {
		// DOWN
		$this->removeIndexFromTable('favorite', 'object_id');
		$this->removeIndexFromTable('favorite', 'user_id');
		$this->removeIndexFromTable('favorite', 'is_deleted');
	}

}
