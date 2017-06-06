<?php

class Adminlookupseeding extends CmfiveMigration {

	public function up() {
		$table = $this->table('lookup');
		
		$rows = [
			// inserting Yes / No lookups
			[ 'YesNo','1','Yes'],
			[ 'YesNo','0','No'],
			// inserting person honorific lookups
			[ 'title','Mr','Mr'],
			[ 'title','Mrs','Mrs'],
			[ 'title','Ms','Ms'],
		];
		
		$table->insert(['type','code','title'],$rows);
		$table->saveData();
		
	}

	public function down() {
		$this->execute("DELETE FROM lookup where type in ('YesNo','title')");
	}

}
