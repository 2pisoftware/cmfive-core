<?php

class CommentService extends DbService {
    
    public function getCommentsForTable($table = null, $object_id = null){
        $where = array("is_deleted = NULL OR is_deleted = ?" => 0);
        if (!empty($table)){
            if (is_a($table, "DbObject")){
                // This way is probably better cause you dont hard code the table name in anywhere
                $where["obj_table"] = $table->getDbTableName();
            } else {
                $where["obj_table"] = $table;
            }
            if (!empty($object_id)){
                $where["obj_id"] = $object_id;
            }
            return $this->getObjects("Comment", $where);
        }
        return null;
    }
    
    public function getComment($id = null){
        if (!empty($id)){
            return $this->getObject("Comment", array("id" => intval($id)));
        }
        return null;
    }
    
	/**
	 * Counts the number of comments created by a user for a given object
	 * 
	 * @param DbObject $object
	 * @param User $user
	 * @return int
	 */
	public function countCommentsForObjectByUser($object, $user) {
		if (empty($object) || empty($user) || !is_a($object, "DbObject")) {
			return 0;
		}
		
		return $this->w->db->get("comment")->where("obj_table", $object->getDbTableName())
				->where("obj_id", $object->id)
				->where("creator_id", $user->id)
				->where("is_system", 0)->count();
	}
	
    /**
     * An easy way to save a comment against an object
     * @param <DbObject> $object
     * @param <String> $message
     */
    public function addComment($object, $message) {
        $comment = new Comment($this->w);
        $comment->obj_table = $object->getDbTableName();
        $comment->obj_id = $object->id;
        $comment->comment = strip_tags($message);
		$comment->is_deleted = 0;
        $comment->insert();
    }
    
    public function renderComment($text) {
    	require_once 'creole/creole.php';
    	return (new creole())->parse(strip_tags($text));
    }
    
}