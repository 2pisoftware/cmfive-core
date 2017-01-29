<?php

class FavoriteInitialMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
        $column->setName('id')
               ->setType('biginteger')
               ->setIdentity(true);

		if (!$this->hasTable('favorite')) {
			$this->table('favorite', [
				'id'          => false,
				'primary_key' => 'id'
			])->addColumn($column)
				->addColumn('object_class', 'string', ['limit' => 255])
				->addColumn('object_id', 'biginteger')
				->addColumn('user_id', 'biginteger')
				->addCmfiveParameters()
				->create();
		}
	}

	public function down() {
		$this->hasTable('favorite') ? $this->dropTable('favorite') : null;
	}

}
