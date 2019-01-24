<?php

class SearchMySQLStopwords extends CmfiveMigration {

	public function up() {
		// UP
	}

	public function down() {
		// DOWN
	}

	public function postText()
	{
		return "<div style='color:red;'>REQUIRED: </div> Go to \modules\search\install\customStopwordSetup.txt for instructions on how to disable the default MySQL stopwords.";
	}

	public function description()
	{
		return "Blank Migration that gives instructions on how disable MySQL default stopwords.";
	}

}
