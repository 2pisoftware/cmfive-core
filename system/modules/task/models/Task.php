<?php

// defines tasks. tasks are associated with a task group and have various attributes
// such as status, priority and current assignee, etc
class Task extends DbObject {

    public $parent_id;   // Parent Task ID.
    public $title;    // not null
    public $task_group_id;  // can be null!
    public $status;   // text
    public $priority;   // text
	public $effort;		// float (null)
    public $task_type;   // text
    public $assignee_id;  // who is currently assigned
    public $dt_assigned;  // date & time of current (last) assignment
    public $dt_first_assigned; // date & time when first assigned
    public $first_assignee_id; // who it was assigned to first
    public $dt_completed;  // date & time when completed
    public $is_closed;   // is 1 if this task is closed
    public $dt_planned;  // date & time planned
    public $dt_due;   // date & time due
    public $estimate_hours; // number of hours estimated
    public $description;
    public $latitude;
    public $longitude;
    public $is_deleted;  // is_deleted flag
    public $_modifiable;  // Modifiable Aspect
    public $_searchable;
    public $rate; //rate used for calculating invoice values
    public static $_validation = array(
        "title" => array('required'),
        "task_group_id" => array('required'),
        "status" => array('required'),
        "task_type" => array('required')
    );
    public static $_db_table = "task";

    /**
	 * Adds task type and task data to the index
	 * 
	 * @return string
	 */
    function addToIndex() {
        $ttype = $this->getTaskTypeObject();
		$index = [];
        if ($ttype) {
            $index[] = $ttype->addToIndex($this);
        }
		
		$data = $this->getTaskData();
		if (!empty($data)) {
			foreach($data as $d) {
				$index[] = $d->addToIndex();
			}
		}
		return implode(' ', $index);
    }

    public function __get($name) {
        // preload taskgroup if its called for
        if ($name === "_taskgroup") {
            $this->_taskgroup = $this->getTaskGroup();
            return $this->_taskgroup;
        } else {
            return parent::__get($name);
        }
    }

	public function isUrgent() {
		$taskgroup_type_object = $this->w->Task->getTaskGroupTypeObject($this->_taskgroup->task_group_type);
		return $taskgroup_type_object->isUrgentPriority($this->priority);
	}

    /**
     * Return a html string which will be displayed alongside
     * the generic task details.
     * 
     */
    function displayExtraDetails() {
        $ttype = $this->getTaskTypeObject();
        if ($ttype) {
            return $ttype->displayExtraDetails($this);
        }
    }

	/**
	 * Retuns all TaskData associated with this task
	 * 
	 * @return array<TaskData>
	 */
	function getTaskData() {
		return $this->getObjects("TaskData", ["task_id" => $this->id]);
	}
	
    /**
     * return the value of task data given the task ID and the key/name of the target attribute
     * task data is associated with the additional form fields available to a task type
     */
    function getDataValue($key) {
        if ($this->id) {
            $c = $this->Task->getObject("TaskData", array("task_id" => $this->id, "data_key" => $key));
            if ($c) {
                return $c->value;
            }
        }
    }

    /**
     * 
     * Set an extra data value field
     * 
     * @param unknown_type $key
     * @param unknown_type $value
     */
    function setDataValue($key, $value) {
        if ($this->id) {
            $c = $this->Task->getObject("TaskData", array("task_id" => $this->id, "data_key" => $key));
            if ($c) {
                $c->value = $value;
                $c->update();
            } else {
                $c = new TaskData($this->w);
                $c->data_key = $key;
                $c->value = $value;
                $c->task_id = $this->id;
                $c->insert();
            }
        }
    }

    // get my membership object and compare my role with that required to view tasks given a task group ID
    function getCanIView() {
        $loggedin_user = $this->w->Auth->user();
        if (empty($loggedin_user->id)) {
            return false;
        }
        
        if ($loggedin_user->is_admin == 1) {
            return true;
        }

		if ($loggedin_user->hasRole("task_admin")) {
			return true;
		}
		
        $me = $this->Task->getMemberGroupById($this->task_group_id, $loggedin_user->id);

        if (empty($me)) {
            return false;
        }

        if ($loggedin_user->id == $this->assignee_id) {
            return true;
        }

        if ($loggedin_user->id == $this->getTaskCreatorId()) {
            return true;
        }

        $group = $this->Task->getTaskGroup($this->task_group_id);
        return $this->Task->getMyPerms($me->role, $group->can_view);
    }
    
    /**
     * used to hide the rate field
     * @return boolean 
     */
    function canISetRate() {
        $user = $this->w->auth->User();
        $taskgroup = $this->getTaskGroup();
        if (!empty($taskgroup) && !empty($user)) {
            if ($user->is_admin == 1 || $taskgroup->isOwner($user)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Used by the search interface
     * @see DbObject::canView()
     */
    function canView(User $user) {
        return $this->getCanIView();
    }

    /**
     * Used by the search interface
     * @see DbObject::canList()
     */
    function canList(User $user) {
        return $this->getCanIView();
    }

    /**
	 * The following users can delete a task:
	 * - Administrators
	 * - Users with the task_admin role
	 * - Task group owners
	 * - Task owners
	 * 
	 * @param \User $user
	 * @return boolean
	 */

    function canDelete(\User $user) {
		// User is admin
		if ($user->is_admin) {
			return true;
		}
		
		// User has role task_admin
		if ($user->hasRole("task_admin")) {
			return true;
		}
		
		// User is taskgroup owner
		$taskgroup = $this->getTaskgroup();
		if ($taskgroup->isOwner($user)) {
			return true;
		}

		// User is task creator
		$creator = $this->_modifiable->getCreator();
		if (!empty($creator) && $creator->id === $user->id) {
			return true;
		}
		
        return false;
    }
    
    // get my membership object and check i am better than GUEST of a task group given a task group ID
    function getCanIEdit() {
        return $this->getCanIAssign();
    }

    // get my membership object and compare my role with that required to assigne tasks given a task group ID
    function getCanIAssign() {
        if ($this->Auth->user()->is_admin == 1) {
            return true;
        }
        $me = $this->Task->getMemberGroupById($this->task_group_id, $_SESSION['user_id']);
        $group = $this->Task->getTaskGroup($this->task_group_id);

        return $this->Task->getMyPerms($me->role, $group->can_assign);
    }

    // if i am assignee, creator or task group owner, i can set notifications for this Task
    function getCanINotify() {
        if ($this->Auth->user()->is_admin == 1) {
            return true;
        }

        $logged_in_user_id = $this->w->Auth->user()->id;
        $me = $this->Task->getMemberGroupById($this->task_group_id, $logged_in_user_id);

        if (($logged_in_user_id == $this->assignee_id) || ($logged_in_user_id == $this->getTaskCreatorId()) || (!empty($me->role) && $this->w->Task->getMyPerms($me->role, "OWNER"))) {
            return true;
        }
        return false;
    }

    // return the ID of the task creator given a task ID
    function getTaskCreatorId() {
        $c = $this->Task->getObject("ObjectModification", array("object_id" => $this->id, "table_name" => $this->getDbTableName()));
        return $c ? $c->creator_id : "";
    }

    // return the name for display of the task creator given a task ID
    function getTaskCreatorName() {
        // I've moved the creator_id to tasks but this is for backwards compatability
        $creator = null;
        if (empty($this->creator_id)) {
            $c = $this->Task->getObject("ObjectModification", array("object_id" => $this->id, "table_name" => $this->getDbTableName()));
            if (!empty($c->creator_id)) {
                $creator = $this->Auth->getUser($c->creator_id);
            }
        } else {
            $creator = $this->Auth->getUser($this->creator_id);
        }

        return $creator ? $creator->getFullName() : "";
    }

    // return the task group title given a task group type
    function getTypeTitle() {
        $c = $this->Task->getTaskTypeObject($this->task_type);
        return (!empty($c) ? $c->getTaskTypeTitle() : null);
    }

    // return the task group description given the task group type
    function getTypeDescription() {
        $c = $this->Task->getTaskTypeObject($this->task_type);
        return (!empty($c) ? $c->getTaskTypeDescription() : null);
    }

    // return the task group title given a task group ID
    function getTaskGroupTypeTitle() {
        return (!empty($this->_taskgroup->id) ? $this->_taskgroup->title : null);
    }

    // return the task types as array for a task group given a task group ID
    function getTaskGroupTypes() {
        return (!empty($this->_taskgroup->id) ? $this->_taskgroup->getTypes() : null);
    }

    // return the task statuses as array for a task group given a task group ID
    function getTaskGroupStatus() {
        return (!empty($this->_taskgroup->id) ? $this->_taskgroup->getTypeStatus() : null);
    }

    // status array has the form array(<status>,true|false);
    // get status types for a task group given a task group ID
    // given a status, return true| false ... $c[<status>] = true|false
    function getisTaskClosed() {
		if ($this->is_closed !== NULL) {
			return $this->is_closed;
		}
		
        if (!empty($this->_taskgroup->id)) {
            $statlist = $this->_taskgroup->getStatus(); //Task->getTaskStatus($this->w->Task->getTaskGroupTypeById($this->task_group_id));
            if ($statlist) {
                foreach ($statlist as $stat) {
                    $status[$stat[0]] = $stat[1];
                }
                return (!empty($status[$this->status]) ? $status[$this->status] : null);
            }
        }
    }

    // return the task priorities as array given a task group ID
    function getTaskGroupPriority() {
        return (!empty($this->_taskgroup->id) ? $this->_taskgroup->getPriority() : null); //Task->getTaskPriority($this->w->Task->getTaskGroupTypeById($this->task_group_id));
    }

    // return list of time log entries for a task given task ID
    function getTimeLogEntries($id = null) {
        if (empty($id)) {
            $id = $this->id;
        }
        return $this->getObjects("timelog", array("object_class" => "Task", "object_id" => $id, "is_deleted" => 0));
            
    }

    // return list of task time log entries, sort by start date
    function getTimeLog() {
        $timelog = $this->getTimeLogEntries($this->id);

        if ($timelog) {
            usort($timelog, array("TaskService", "sortByStarted"));
        }

        return $timelog;
    }

    // return due date in bold red for display, if it is on or past the due date
    function isTaskLate() {
        if (($this->dt_due == "0000-00-00 00:00:00") || ($this->dt_due == "")) {
			return "<em>" . formatDate($this->_modifiable->getCreatedDate()) . " (Created)</em>";
//            return "Not given";
		}
		
        if ((!$this->getisTaskClosed()) && (time() > $this->dt_due)) {
            return "<font color=red><b>" . formatDate($this->dt_due) . "</b></font>";
        } else {
            return formatDate($this->dt_due);
        }
    }

    // return a task type object given a task type
    function getTaskTypeObject() {
        if ($this->task_type) {
            return $this->Task->getTaskTypeObject($this->task_type);
        }
    }

    function printSearchTitle() {
        $buf = (!empty($this->title) ? $this->title : 'Task [' . $this->id . ']') . ', ' . strtoupper($this->status);
        return $buf;
    }

    function printSearchListing() {
        $tg = $this->Task->getTaskGroup($this->task_group_id);
        $assignee = $this->getAssignee();
        $buf = $tg->title;
        if ($assignee) {
            $buf .= ', Assigned: ' . $assignee->getFullName();
        }

        if ($this->dt_first_assigned) {
            $buf .= ', First Assigned: ' . $this->getDate('dt_first_assigned');
        }

        if ($this->dt_due) {
            $buf .= ', Due: ' . $this->getDate('dt_due');
        }
        return $buf;
    }

    function printSearchUrl() {
        return "task/edit/" . $this->id;
    }

	function toLink($class = null, $target = null, $user = null) {
        if (empty($user)) {
            $user = $this->w->Auth->user();
        }
        if ($this->canView($user)) {
            return Html::a($this->w->localUrl($this->printSearchUrl()), (!empty($this->title) ? htmlentities($this->title) : 'Task [' . $this->id . ']'), null, $class, null, $target);
        }
        return (!empty($this->title) ? htmlentities($this->title) : 'Task [' . $this->id . ']');
    }
	
    function getAssignee() {
        if (!empty($this->assignee_id)) {
            return $this->getObject("User", $this->assignee_id);
        }
    }

    function isStatusClosed() {
    	$tg = $this->getTaskGroup();
    	return $tg->isStatusClosed($this->status);
    }
    
    /**
     * (non-PHPdoc)
     * @see DbObject::insert()
     */
    function insert($force_validation = false) {
        try {
            $this->startTransaction();

            // 1. Call on_before_insert of the TaskGroupType
			
            $tg = $this->getTaskGroup();
            if (!empty($tg)) {

                if ($this->isStatusClosed()) {
                    $this->is_closed = 1;
                } else {
                	$this->is_closed = 0;
                }

                $tg_type = $tg->getTaskGroupTypeObject();

                // check for and set default status
                if (empty($this->status)) {
                    $this->status = $tg_type->getDefaultStatus();
                }

                $tg_type->on_before_insert($this);
            }

            // 2. Call on_before_insert of the Tasktype

            if ($this->task_type) {
                $this->getTaskTypeObject()->on_before_insert($this);
            }

            // 3. insert task into database
            $validation_response = parent::insert($force_validation);
            if ($validation_response !== true) {
                $this->rollbackTransaction();
                $this->w->errorMessage($this, "Task", $validation_response, false, "/tasks/edit");
            }

			if (empty($this->title)) {
				$this->update();
			}
			
            // run any post-insert routines
            // add a comment upon task creation
            $comm = new TaskComment($this->w);
            $comm->obj_table = $this->getDbTableName();
            $comm->obj_id = $this->id;
			$comm->is_system = 1;
            $comm->comment = "Task Created";
            $comm->insert();

            // add to context for notifications post listener
            $this->w->ctx("TaskComment", $comm);
            $this->w->ctx("TaskEvent", "task_creation");

            // 4. Call on_after_insert of TaskType

            if ($this->task_type) {
                $this->getTaskTypeObject()->on_after_insert($this);
            }

            // 5. Call on_after_insert of the TaskGroupType

            if (!empty($tg_type)) {
                $tg_type->on_after_insert($this);
            }

            $this->commitTransaction();
        } catch (Exception $ex) {
            $this->Log->error("Inserting Task: " . $ex->getMessage());
            $this->rollbackTransaction();
        }
    }

    /**
     * (non-PHPdoc)
     * @see DbObject::update()
     */
    function update($force = false, $force_validation = false) {

    	// 0. set the is_closed flag to make sure the task can be queried easily
    	
    	if ($this->isStatusClosed()) {
    		$this->is_closed = 1;
    	} else {
    		$this->is_closed = 0;
    	}
    	
        try {
            $this->startTransaction();

            // 1. Call on_before_update of the TaskGroupType

            $tg = $this->getTaskGroup();
            if (!empty($tg)) {
                $tg_type = $tg->getTaskGroupTypeObject();
                $tg_type->on_before_update($this);
            }

            // 2. Call on_before_update of the Tasktype

            if ($this->task_type) {
                $this->getTaskTypeObject()->on_before_update($this);
            }

            // 3. update the task
			if (empty($this->title)) {
				$this->title = 'Task [' . $this->id . ']';
			}
			
            $validation_response = parent::update($force, $force_validation);
            if ($validation_response !== true) {
                $this->rollbackTransaction();
                $this->Log->error("Task update failed validation, rolling back transaction");
                $this->w->errorMessage($this, "Task", $validation_response, false, "/tasks/edit/" . $this->id);
            }

            // 4. Call on_after_update of the TaskType

            if ($this->task_type) {
                $this->getTaskTypeObject()->on_after_update($this);
            }

            // 5. Call on_after_update of the TaskGroupType

            if (!empty($tg_type)) {
                $tg_type->on_after_update($this);
            }

            $this->commitTransaction();
        } catch (Exception $ex) {
            $this->Log->error("Updating Task(" . $this->id . "): " . $ex->getMessage());
            $this->rollbackTransaction();
        }
    }

    /**
     * (non-PHPdoc)
     * @see DbObject::delete()
     */
    function delete($force = false) {
        try {
            $this->startTransaction();

            // 1. Call on_before_delete of the TaskGroupType

            $tg = $this->getTaskGroup();
            if (!empty($tg)) {
                $tg_type = $tg->getTaskGroupTypeObject();
                $tg_type->on_before_delete($this);
            }

            // 2. Call on_before_delete of the TaskType

            if ($this->task_type) {
                $this->getTaskTypeObject()->on_before_delete($this);
            }

            // 3. Delete the task

            parent::delete($force);

            // 4. Call on_after_delete of the TaskType

            if ($this->task_type) {
                $this->getTaskTypeObject()->on_after_delete($this);
            }

            // 5. Call on_after_delete of the TaskGroupType

            if (!empty($tg_type)) {
                $tg_type->on_after_delete($this);
            }

            $this->commitTransaction();
        } catch (Exception $ex) {
            $this->Log->error("Deleting Task(" . $this->id . "): " . $ex->getMessage());
            $this->rollbackTransaction();
        }
    }

    function getTaskGroup() {
        return $this->Task->getTaskGroup($this->task_group_id);
    }

    function getIcal() {
        if (empty($this->id) || empty($this->dt_due)) {
            return null;
        }
        
        $date = date("Ymd", strtotime(str_replace('/', '-', $this->dt_due)));

		$task_creator = $this->getCreator();
		$task_assignee = $this->getAssignee();
		
		$assignee_name = '';
		$mailto_email = '';
		if (!empty($task_assignee)) {
			$assignee_name = $task_assignee->getFullName();
			$mailto_email = $task_assignee->getContact()->email;
		} else {
			$mailto_email = $task_creator->getContact()->email;
		}
        // Borrowed from here http://stackoverflow.com/questions/1463480/how-can-i-use-php-to-dynamically-publish-an-ical-file-to-be-read-by-google-calen
        $ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
CALSCALE:GREGORIAN
METHOD:REQUEST
BEGIN:VEVENT
DTSTART;VALUE=DATE:" . $date . "
DTEND;VALUE=DATE:" . $date . "
DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') . "Z
ORGANIZER;CN=" . $task_creator->getFullName() . ":mailto:" . $task_creator->getContact()->email . "
UID:" . md5(uniqid(mt_rand(), true)) . "@2pisoftware.com
ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=
  TRUE;CN=" . $assignee_name . ";X-NUM-GUESTS=0:mailto:" . $mailto_email . "
SUMMARY:" . $this->title . "
DESCRIPTION:" . htmlentities($this->description) . "
SEQUENCE:0
STATUS:CONFIRMED
END:VEVENT
END:VCALENDAR";
        
        return $ical;
    }
    
}
