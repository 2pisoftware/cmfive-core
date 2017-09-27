<?php

class TagRemoveObsoleteColumns extends CmfiveMigration {

	public function up() {
		//alter orginal tag table
        $this->removeColumnFromTable('tag', 'user_id');
        $this->removeColumnFromTable('tag', 'obj_class');
        $this->removeColumnFromTable('tag', 'obj_id');
        $this->removeColumnFromTable('tag', 'tag_color');
	}

	public function down() {
		$this->addColumnTotable('tag', 'user_id', 'biginteger', ['null' => true]);
        $this->addColumnTotable('tag', 'obj_class', 'string', ['null' => true]);
        $this->addColumnTotable('tag', 'obj_id', 'biginteger', ['null' => true]);
        $this->addColumnTotable('tag', 'tag_color', 'string', ['null' => true]);
	}

}