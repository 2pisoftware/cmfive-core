<?php

class TagService extends DbService {
	
	/**
	 * Gets tag by ID
	 * 
	 * @param int $id
	 * @return Tag
	 */
	public function getTag($id){
		return $this->getObject("Tag", $id);
	}
	
	/**
	 * Gets all current tags
	 * 
	 * @return Array<Tag>
	 */
	public function getTags() {
		return $this->getObjects('Tag', ['is_deleted' => 0]);
	}
	

	/**
	 * Returns tags linked to an object
	 * 
	 * @param type $object_id
	 * @param type $class
	 * @return Array<Tag>
	 */
	public function getTagsByObject($object) {
		if (!empty($object->id) && $object instanceof DbObject) {
			$query = $this->db->get('tag')->leftJoin('tag_assign on tag.id = tag_assign.tag_id')
				->where('object_class', get_class($object))->and('object_id', $object->id)
				->and('tag.is_deleted', 0)->and('tag_assign.is_deleted', 0)->fetchAll();
			
			return $this->getObjectsFromRows('Tag', $query);
		}
		return null;
	}
	
	/**
	 * Returns all tags associated with a given class
	 * 
	 * @param String $object_class
	 * @return Array<Tag>
	 */
	public function getTagsByObjectClass($object_class) {
		$query = $this->db->get('tag')->leftJoin('tag_assign on tag.id = tag_assign.tag_id')
				->where('object_class', $object_class)
				->and('tag.is_deleted', 0)->and('tag_assign.is_deleted', 0)->orderBy('tag ASC')->fetchAll();
		return $this->getObjectsFromRows('Tag', $query);
	}
	
	public function getAllTags($returnObjects = false) {
		//Loads a list of all tags that were ever created
		
		return !!$returnObjects ? $this->getObjects('Tag', ['is_deleted' => 0]) : $this->_db->get('tag')->where('is_deleted', 0)->orderBy('tag ASC')->fetch_all();
	}
	
	public function navigation(Web $w, $title = null, $nav = null) {
        if ($title) {
            $w->ctx("title", $title);
        }

        $nav = $nav ? $nav : array();

        if ($w->Auth->loggedIn() && $this->w->Auth->user()->hasRole('tag_admin')) {
            $w->menuLink("tag/admin", "Tag Admin", $nav);
        }
        $w->ctx("navigation", $nav);
        return $nav;
    }
}