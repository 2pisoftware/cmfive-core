<?php

class SearchMySQLStopwords extends CmfiveMigration {

	public function up() {
		// UP
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);

		if (!$this->hasTable('search_stopwords')) {
			$this->table('search_stopwords', [
						'id' => false,
						//'primary_key' => 'id'
					])//->addColumn($column)
					->addStringColumn('value')
					->create();
		}
	}

	public function down() {
		// DOWN
		$this->hasTable('search_stopwords') ? $this->dropTable('search_stopwords') : null;
	}

}
