<?php

class SearchMySQLStopwords extends CmfiveMigration {

	public function up() {
		// UP

		if (!$this->hasTable('custom_stopwords_override')) {
			$this->table('custom_stopwords_override', [
						'id' => false,
					])
					->addColumn('value', 'string')
					->create();
				$query = "INSERT INTO custom_stopwords_override(value) VALUE ('dgiwdgwidwdiv2');";
				$this->w->db->query($query)->execute();
		}

		$this->w->Search->reindexAll();
	}

	public function down() {
		// DOWN
		$this->hasTable('custom_stopwords_override') ? $this->dropTable('custom_stopwords_override') : null;
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
