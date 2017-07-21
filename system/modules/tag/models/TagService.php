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
				->and('tag.is_deleted', 0)->and('tag_assign.is_deleted', 0)->fetchAll();
		return $this->getObjectsFromRows('Tag', $query);
	}
	
	
//	public function removeTag($object_id, $obj_class, $tagText) {
//		//No id needed as the other fields act as our unique identifiers
//		if( !empty($object_id) && !empty($obj_class) ) {
//			$tag = $this->getObject("Tag", array("obj_class" => $obj_class, "obj_id" => $object_id, 'tag' => $tagText));
//			if(!empty($tag)) {
//				$tag->delete();
//				//Update objects index and anything else...
//				$obj = $this->getObject($obj_class, $object_id);
//				$obj->update();
//			}
//		}
//	}
	
	public function getAllTags($returnObjects = false) {
		//Loads a list of all tags that were ever created
		
		return !!$returnObjects ? $this->getObjects('Tag', ['is_deleted' => 0]) : $this->_db->get('tag')->where('is_deleted', 0)->orderBy('id, tag')->fetch_all();
//		$unique_tags = [];
//		if (!empty($tags)) {
//			foreach($tags as $tag) {
//				if (!array_key_exists($tag['tag'], $unique_tags)) {
//					$unique_tags[$tag['tag']] = $tag;
//				}
//			}
//		}
//		
//		ksort($unique_tags);
//		
//		if($returnObjects) {
//			if(!empty($unique_tags)) {
//				$objects = $this->getObjectsFromRows('Tag', $unique_tags);
//				return $objects;
//			}
//		}
//		$tagList = array();
//		foreach($unique_tags as $tag) {
//			$tagList[$tag['id']] = $tag;
//		}
//		return $tagList;
	}
	/**
	 * An easy way to save a tag against an object
	 * @param <Int> $object_id
	 * @param <String> $obj_class
	 * @param <String> $tagText
	 * @param <String> $color
	 */
	public function addTag($object_id, $obj_class, $tagText, $color='') {
		//First look for existing deleted record and restore it if found
		//Otherwise create a new record...
		$tagLookup = $this->getObject("Tag", array("obj_class" => $obj_class, "obj_id" => $object_id, 'tag' => $tagText));
		if(!empty($tagLookup)) {
			$tagLookup->is_deleted = 0;
			$tagLookup->update();
			//Update objects index and anything else...
			$obj = $this->getObject($obj_class, $object_id);
			$obj->update();
		} else {
			$tag = new Tag($this->w);
			$tag->obj_class = $obj_class;
			$tag->obj_id = $object_id;
			$user = $this->w->Auth->user();
			$tag->user_id = $user->id;
			$tag->tag_color = $color;
			$tag->tag = trim(strip_tags($tagText));
			$tag->insert();
			//Update objects index and anything else...
			$obj = $this->getObject($obj_class, $object_id);
			$obj->update();
		}
	}
	
//	public function deleteTag($tag) {
//		$tags = $this->getObjects("Tag", array('tag' => $tag));
//		if(!empty($tags)) {
//			foreach($tags as $tag) {
//				$tag->delete(true);
//				if(!$tag->is_deleted) {
//					//Update objects index and anything else...
//					$obj = $this->getObject($tag->obj_class, $tag->obj_id);
//					$obj->update();
//				}
//			}
//		}
//	}
//	public function renameTag($tag, $tagText) {
//		//Check if tagText already exists...
//		$tags = $this->getObjects("Tag", array('tag' => trim(strip_tags($tagText))));
//		if(!empty($tags)) {
//			return -1;
//		}
//		$tags = $this->getObjects("Tag", array('tag' => $tag));
//		if(!empty($tags)) {
//			foreach($tags as $tag) {
//				$tag->tag = trim(strip_tags($tagText));
//				$tag->update();
//				if(!$tag->is_deleted) {
//					//Update objects index and anything else...
//					$obj = $this->getObject($tag->obj_class, $tag->obj_id);
//					$obj->update();
//				}
//			}
//		}
//	}
//	
	public function getTagButton($id, $class) {
		$buf = '';
		$user = $this->w->Auth->user();
		if( !empty($user) ) {
			//Check roles access
			//Admin gets to add new tags globally
			//User can attach an existing tag
			//Different scripts handle this functionality - more checks done in action
			if( $user->hasRole("tag_admin") ) {
				$buf .= '<script src="/system/modules/tag/assets/js/tagButtonAdmin.js"></script>';
			} else if( $user->hasRole("tag_user") ) {
				$buf .= '<script src="/system/modules/tag/assets/js/tagButton.js"></script>';
			}
		}
		if( !empty($id) && !empty($class) ) {
			//Load all tags for this object
			$tags = $this->getTagsByObject($id, $class);
			$url = '/tag/ajaxTag/?class='.$class.'&id='.$id;
			//Build list of tags
			$buf .= '<span class="tag_list" data-url="'.$url.'">';
			if( empty($tags) ) {
				$buf .= '<span class="label radius secondary no_tags tag_selection"><span class="fi-price-tag">No tag</span></span> ';
			} else {
				$buf .= '<span style="display:none;" class="label radius secondary no_tags tag_selection"><span class="fi-price-tag">No tag</span></span> ';
				foreach($tags as $tag) {
					$buf .= '<span data-tag="'.$tag->tag.'" class="label radius secondary tag_selection"><span '.(!empty($tag->tag_color) ? 'style="color:'.$tag->tag_color.'"' : '').' class="fi-price-tag">'.$tag->tag.'</span></span> ';
				}
			}
			if( !empty($user) ) {
				//Build tag dialog popup
				//This is empty on load and is dynamically filled
				$tagDialogId = 'tag_list_'.$class.$id;
				$buf .= '<div class="tag_list_dialog" id="'.$tagDialogId.'">
					<div class="tag_list_modal">
						<div class="tag_list_header">Available tags <span class="fi-x hide_tag_list"></span><div><input type="text" placeholder="'.($user->hasRole("tag_admin") ? 'Add / ': '').'Filter tags" class="search_tags" /></div></div>
						<div class="tag_list_body"></div>
					</div>
				</div></span>';
			}
		}
		return $buf;
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