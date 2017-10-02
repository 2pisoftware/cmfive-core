<?php

class AuthContactTitles extends CmfiveMigration {

	public function up() {
		$table = $this->table('contact');
		$table->addColumn('title_lookup_id', 'biginteger', ['after' => 'othername', 'null' => true])
			  ->removeColumn('title')
		      ->update();
	}

	public function down() {
		$table = $this->table('contact');
		$table->removeColumn('title_lookup_id')
			  ->addColumn('title', 'string', ['after' => 'othername', 'limit' => 32, 'null' => true])
			  ->update();
	}

}
