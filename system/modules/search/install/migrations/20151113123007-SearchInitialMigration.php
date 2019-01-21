<?php

class SearchInitialMigration extends CmfiveMigration {

	public $preText = "To disable the default MySQL stopwords, run the file C:\Users\jades\Documents\cmfive-boilerplate\system\modules\search\install\customStopwordSetup.php as a super admin. This will set the MySQL stopwords table to an empty one, thus disabling the default stopwords.";

	public function up() {
		// UP
	}

	public function down() {
		// DOWN
	}

}
