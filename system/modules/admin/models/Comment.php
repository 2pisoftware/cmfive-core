<?php

/*
 * Use of comparison:
 * 
 * class Job:
 * 
 * function getComments()
  {
  $commArray = $this->w->Operations->getOps("OpsComment",array('obj_id'=>$this->id));

  usort($commArray, array("AComment","cmp_obj"));


  return $commArray;
  }

 * */

class Comment extends DbObject {

    public $id;
    public $obj_table;   // varchar      
    public $obj_id;
    public $comment;     // text
    public $is_internal; // 1 - is_internal - will be displayed only for internal roles ; Default is 0.
    public $is_system;   // 1 - is system generated comment (on attachment Upload/Delete); Default is 0.
    
    public $creator_id;
    public $dt_created;
    public $modifier_id;
    public $dt_modified;
    public $is_deleted;

    public static $_db_table = "comment";
        
    /*
     * Output Example:
     * webforum.jpg File deleted. Description: Image of something.
     * By serg_admin-Manager Ops Manager,18/02/2011 03:10 pm
     * */

    public function __toString() {
        $str = $this->comment;
        $u = $this->w->Auth->getUser($this->creator_id);
        if ($u) {
            $str .= "<br>".__("By")." <i>" . $u->getFullName() . ",</i>";
        }
        $str.= "<i>" . formatDateTime($this->dt_created) . "</i>";
        return $str;
    }
    
    /*
     * get object for comment thread
     * return object
     */
    public function getParentObject() {
        if ($this->obj_table == 'comment') {
            return $this->w->Comment->getComment($this->obj_id)->getParentObject();
        } else {
            return $this->w->Comment->getObject($this->obj_table,$this->obj_id);
        }
    }

    /*
     * New comments go First !
     * */

    static function cmp_obj($a, $b) {
        if ($a->dt_created == $b->dt_created) {
            return 0;
        }
        return ($a->dt_created < $b->dt_created) ? +1 : -1;
    }
    
    public function insert($force_validation = true) {
        parent::insert($force_validation);
                
        // Call Hook
        $this->w->callHook("comment", "comment_added_" . $this->obj_table, $this);
    }

}

