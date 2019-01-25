<?php

class SearchMySQLStopwords extends CmfiveMigration {

	public function up() {
		// UP

		if (!$this->hasTable('my_stopwords')) {
			$this->table('my_stopwords', [
						'id' => false,
					])
					->addColumn('value', 'string')
					->create();
				$query = "INSERT INTO my_stopwords(value) VALUE ('dgiwdgwidwdiv2');";
				$this->w->db->query($query)->execute();
		}

		$this->w->Search->reindexAll();
	}

	public function down() {
		// DOWN
		$this->hasTable('my_stopwords') ? $this->dropTable('my_stopwords') : null;
	}

	public function postText()
	{
		return "<div style='color:red;'>REQUIRED: </div> Go to \modules\search\install\customStopwordSetup.txt for instructions on how to disable the default MySQL stopwords.";
	}

	public function description()
	{
		return "Migration that creates the stopwords table to overide the mysql default stopword table. Also reindexes objects in the object_index table to account for the change in the stopwords list.";
	}

}
