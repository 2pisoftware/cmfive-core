<?php

class Adminlookupseeding extends CmfiveMigration {

	public function up() {
		$table = $this->table('lookup');
		
		$rows = [
			// inserting Yes / No lookups
			[ 'type'  	=> 'YesNo',
			  'code' 	=> '1',
			  'title' 	=> 'Yes',
			],
			[ 'type'  	=> 'YesNo',
			  'code' 	=> '0',
			  'title' 	=> 'No',
			],
			// inserting person honorific lookups
			[ 'type'  	=> 'title',
			  'code' 	=> 'Mr',
			  'title' 	=> 'Mr',
			],
			[ 'type'  	=> 'title',
			  'code' 	=> 'Mrs',
			  'title' 	=> 'Mrs',
			],
			[ 'type'  	=> 'title',
			  'code' 	=> 'Ms',
			  'title' 	=> 'Ms',
			],
		];
		
		$table->insert($rows);
		$table->saveData();
		
	}

	public function down() {
		$this->execute("DELETE FROM lookup where type in ('YesNo','title')");
	}

}
