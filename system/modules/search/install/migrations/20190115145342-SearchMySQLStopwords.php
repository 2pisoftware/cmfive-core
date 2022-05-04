<?php

class SearchMySQLStopwords extends CmfiveMigration {

	public function up() {
		// UP
		ini_set('max_execution_time', 0);

		if (!$this->hasTable('custom_stopwords_override')) {
			$this->table('custom_stopwords_override', [
						'id' => false,
					])
					->addColumn('value', 'string')
					->create();
				$query = "INSERT INTO custom_stopwords_override(value) VALUE ('dgiwdgwidwdiv2');";
				$this->w->db->sql($query);
		$query = "ALTER TABLE object_index ENGINE = InnoDB;";
		$this->w->db->sql($query);
		}
		
	}

	public function down() {
		// DOWN
		$this->hasTable('custom_stopwords_override') ? $this->dropTable('custom_stopwords_override') : null;
	}

	public function postText()
	{
		return "Make sure to reindex the search functionality by going to admin -> Search Admin and clicking \"Reindex All Search Objects\"";
	}

	public function description()
	{
		return "Migration that creates the stopwords table to overide the mysql default stopword table. Also reindexes objects in the object_index table to account for the change in the stopwords list as well as converting object_index table to InnoDB engine.";
	}

}
