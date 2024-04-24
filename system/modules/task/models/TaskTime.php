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

    public function getTotalTimeByTimeType() {
        $all_tasks = TaskService::getInstance($this->w)->getTasks();
        foreach ($all_tasks as $task) {
            if ($task->id == $this->task_id) {
                $current_task = $task;
            }
        }
        $all_timelogs_for_task = TimelogService::getInstance($this->w)->getTimelogsForObject($current_task);
        
        $timelog_types = Config::get('task.TaskType_' . $current_task->task_type)['time-types'];
        $time_totals_for_time_types = [];
        foreach ($timelog_types as $timelog_type) {
            $total_time_for_type = 0;
            foreach ($all_timelogs_for_task as $timelog) {
                if ($timelog->time_type == $timelog_type) {
                    $total_time_for_type += $timelog->getDuration();
                }
                $total_time_fmtd = floor($total_time_for_type / 3600) . gmdate(":i:s", $total_time_for_type) . '  ';
            }
            $time_totals_for_time_types[$timelog_type] = $total_time_fmtd;
        }
        return $time_totals_for_time_types;
    }

    public function getTotalTimeByBillable() {
        $time_totals = $this->getTotalTimeByTimeType();
        $time_totals_in_seconds = [];
        foreach ($time_totals as $time_total) {
            $time_totals_in_seconds[] = strtotime("1970-01-01 $time_total UTC");
        }
        $billable_time_in_seconds = array_sum(array_merge(array_slice($time_totals_in_seconds, 0, 3), [$time_totals_in_seconds[4]]));
        $billable_time = floor($billable_time_in_seconds / 3600) . gmdate(":i:s", $billable_time_in_seconds);
        $nonbillable_time = floor($time_totals_in_seconds[3] / 3600) . gmdate(":i:s", $time_totals_in_seconds[3]);
        return ['Billable' => $billable_time, 'Non-Billable' => $nonbillable_time];
    }

}
