<?php
class TaskTime extends  DbObject {
    public $task_id;
    public $creator_id;	// who created this time entry
    public $dt_created;	// when this time entry was created
    public $user_id;		// who accrued this time (most often == creator_id, but not necessarily!)
    public $dt_start;		// start of time period
    public $dt_end;		// end of time period
    public $comment_id; 	// id of comment associated with this log entry
    public $is_suspect;	// suspect/accept toggle
    public $is_deleted;	// is deleted flag
    public $time_type;

    public static $_db_table = "task_time";

    public function getDuration() {
        if (!empty($this->dt_start) and !empty($this->dt_end)) {
            return ($this->dt_end - $this->dt_start);
        }
    }
    
    public function getComment() {
        if (!empty($this->comment_id)) {
            return CommentService::getInstance($this->w)->getComment($this->comment_id);
        }
    }
    
    public function getTask() {
        return $this->getObject("Task", $this->task_id);
    }
    
}
