<?php

class TagAbstractMigration extends CmfiveMigration {

	public function up() {
		$column = parent::Column();
        $column->setName('id')
               ->setType('biginteger')
               ->setIdentity(true);

		if (!$this->hasTable("tag_assign")) {
			$this->table('tag_assign', [
				'id'          => false,
				'primary_key' => 'id'
			])->addColumn($column)
				->addColumn('object_class', 'string', ['limit' => 200])
				->addColumn('object_id', 'biginteger', ['null' => true])
				->addColumn('tag_id', 'string', ['limit' => 255])
				->addCmfiveParameters()
				->addIndex(['object_class', 'object_id', 'tag_id'])
				->create();
			
			// Move data from tags to tag assign
			// Ensure tags are unique on Tag table
			$unique_tags = [];
			if ($this->table('tag')->hasColumn('obj_class')) {
				$existing_tags = $this->w->db->get('tag')->fetchAll();
				if (!empty($existing_tags)) {
					foreach($existing_tags as $existing_tag) {
						if (empty($existing_tag['tag'])) {
							continue;
						}
						if (!array_key_exists($existing_tag['tag'], $unique_tags)) {
							$unique_tags[$existing_tag['tag']] = [];
						}
						$unique_tags[$existing_tag->tag][] = ['object_class' => $existing_tag['obj_class'], 'object_id' => $existing_tag['obj_id']];
					}
					
					// Remove existing tags
					$this->w->db->delete('tag')->execute();
					
					foreach($unique_tags as $unique_tag => $assigned_objects) {
						$tag_object = new Tag($this->w);
						$tag_object->tag = $unique_tag;
						$tag_object->insert();
						
						foreach($assigned_objects as $assigned_object) {
							$tag_assign = new TagAssign($this->w);
							$tag_assign->tag_id = $tag_object->id;
							$tag_assign->object_class = $assigned_object['object_class'];
							$tag_assign->object_id = $assigned_object['object_id'];
							$tag_assign->insert();
						}
					}
				}
			}
			
		}
	}

	public function down() {
		
	}

}
