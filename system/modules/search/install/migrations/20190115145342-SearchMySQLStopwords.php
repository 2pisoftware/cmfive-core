<?php

class SearchMySQLStopwords extends CmfiveMigration {

	public function up() {
		// UP
		$this->w->Search->reindexAll();
	}

	public function down() {
		// DOWN
	}

	public function preText()
	{
		return "<div style='color:red;'>REQUIRED: </div> Go to \modules\search\install\customStopwordSetup.txt for instructions on how to disable the default MySQL stopwords before running this migration.";
	}

	public function description()
	{
		return "Migration that gives instructions on disabling default mysql stopwords. Also reindexes object_index table to account of the change in the stopwords list.";
	}

}
