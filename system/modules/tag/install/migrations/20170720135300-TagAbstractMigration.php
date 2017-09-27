<?php

class TagAbstractMigration extends CmfiveMigration {

	public function up() {
        ini_set('max_execution_time', 300);
        
		$column = parent::Column();
        $column->setName('id')
               ->setType('biginteger')
               ->setIdentity(true);

		if (!$this->hasTable("tag_assign")) {
			$this->table('tag_assign', [
				'id'          => false,
				'primary_key' => 'id'
			])->addColumn($column)
				->addColumn('object_class', 'string', ['limit' => 200, 'null' => true, 'default' => null])
				->addColumn('object_id', 'biginteger', ['null' => true, 'default' => null])
				->addColumn('tag_id', 'string', ['limit' => 255, 'null' => true, 'default' => null])
				->addCmfiveParameters()
				->addIndex(['object_class', 'object_id', 'tag_id'])
				->create();
			
			// Move data from tags to tag assign
			// Ensure tags are unique on Tag table
			$unique_tags = [];
			if ($this->table('tag')->hasColumn('obj_class')) {
				$existing_tags = $this->w->db->get('tag')->fetchAll();
				$this->removeColumnFromTable('tag', 'user_id');
		        $this->removeColumnFromTable('tag', 'obj_class');
		        $this->removeColumnFromTable('tag', 'obj_id');
		        $this->removeColumnFromTable('tag', 'tag_color');
				if (!empty($existing_tags)) {
					foreach($existing_tags as $existing_tag) {
						if (empty($existing_tag['tag'])) {
							continue;
						}
						if (!array_key_exists($existing_tag['tag'], $unique_tags)) {
							$unique_tags[$existing_tag['tag']] = [];
						}
                        if ($existing_tag['is_deleted'] == 0) {
    						$unique_tags[$existing_tag['tag']][] = ['object_class' => $existing_tag['obj_class'], 'object_id' => $existing_tag['obj_id']];
                        }
					}
					
					// Remove existing tags
					$this->w->db->delete('tag')->execute();
                    
					foreach($unique_tags as $unique_tag => $assigned_objects) {
						$tag_object = new Tag($this->w);
						$tag_object->tag = $unique_tag;
						$tag_object->insert();
						
						foreach($assigned_objects as $assigned_object) {
							if (!empty($assigned_object['object_class']) && !empty($assigned_object['object_id'])) {
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
	}

	public function down() {
		$tags = $this->w->db->get('tag')->select('tag_assign.*')->leftJoin('tag_assign on tag.id = tag_assign.tag_id')
				->where('tag.is_deleted', 0)->and('tag_assign.is_deleted', 0)->fetchAll();
        
        // Remove existing tags
        $this->w->db->delete('tag')->execute();
        
        if( !empty($tags)) {
            foreach ($tags as $tag) {
                $tag_object = new Tag($this->w);
                $tag_object->tag = $tag['tag'];
                $tag_object->obj_class = $tag['object_class'];
                $tag_object->obj_id = $tag['object_id'];
                $tag_object->insert();
            }
        }
        
        $this->dropTable('tag_assign');
	}

}
