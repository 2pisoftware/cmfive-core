<?php
/**
 * Comments can be made on various objects
 */
class Comment extends DbObject
{
    public $id;
    public $obj_table; // varchar
    public $obj_id;
    public $comment; // text
    public $is_internal; // 1 - is_internal - will be displayed only for internal roles ; Default is 0.
    public $is_system; // 1 - is system generated comment (on attachment Upload/Delete); Default is 0.

    public $creator_id;
    public $dt_created;
    public $modifier_id;
    public $dt_modified;
    public $is_deleted;

    public static $_db_table = "comment";
    public $_restrictable;

    /**
     * Output Example:
     * webforum.jpg File deleted. Description: Image of something.
     * By serg_admin-Manager Ops Manager,18/02/2011 03:10 pm
     */
    public function __toString()
    {
        $str = $this->comment;
        $u = AuthService::getInstance($this->w)->getUser($this->creator_id);
        if ($u) {
            $str .= "<br>By <i>" . $u->getFullName() . ",</i>";
        }
        $str .= "<i>" . formatDateTime($this->dt_created) . "</i>";
        return $str;
    }

    /**
     * get object for comment thread
     * return object
     */
    public function getParentObject()
    {
        if ($this->obj_table == 'comment') {
            return CommentService::getInstance($this->w)->getComment($this->obj_id)->getParentObject();
        } else {
            $class = str_replace(' ', '', $this->getHumanReadableAttributeName($this->obj_table));
            if (class_exists($class)) {
                return CommentService::getInstance($this->w)->getObject($class, $this->obj_id);
            } else {
                return null;
            }
        }
    }

    /**
     * New comments go First !
     */
    public static function cmp_obj($a, $b)
    {
        if ($a->dt_created == $b->dt_created) {
            return 0;
        }
        return ($a->dt_created < $b->dt_created) ? +1 : -1;
    }

    public function insert($force_validation = true)
    {
        $result = parent::insert($force_validation);
        $this->w->callHook("comment", "comment_added_" . $this->obj_table, $this);
        return $result;
    }
}
