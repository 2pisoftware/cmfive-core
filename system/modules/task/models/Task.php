<?php
/**
 * defines tasks. tasks are associated with a task group and have various attributes
 * such as status, priority and current assignee, etc
 */
class Task extends DbObject
{
    public $parent_id; // Parent Task ID.
    public $title; // not null
    public $task_group_id; // can be null!
    public $status; // text
    public $priority; // text
    public $effort; // float (null)
    public $task_type; // text
    public $assignee_id; // who is currently assigned
    public $dt_assigned; // date & time of current (last) assignment
    public $dt_first_assigned; // date & time when first assigned
    public $first_assignee_id; // who it was assigned to first
    public $dt_completed; // date & time when completed
    public $is_closed; // is 1 if this task is closed
    public $dt_planned; // date & time planned
    public $dt_due; // date & time due
    public $estimate_hours; // number of hours estimated
    public $description;
    public $latitude;
    public $longitude;
    public $is_deleted; // is_deleted flag
    public $_modifiable; // Modifiable Aspect
    public $_searchable;
    public $rate; //rate used for calculating invoice values
    public $is_active;
    public static $_validation = [
        "title" => ['required'],
        "task_group_id" => ['required'],
        "status" => ['required'],
        "task_type" => ['required'],
    ];
    public static $_db_table = "task";

    /**
     * Used by the task_core_dbobject_after_insert_task hook to skip sending notifications if true
     * @var boolean
     */
    public $_skip_creation_notification;

    /**
     * get all of the subscribers for a task
     *
     * @return TaskSubscriber[]
     */
    public function getSubscribers()
    {
        return $this->getObjects('TaskSubscriber', ['task_id' => $this->id, 'is_deleted' => 0]);
    }

    /**
     * add a subscriber to a task, if they aren't already subscribed
     *
     * @param User $user
     *
     * @return bool true if the user was not already a subscriber
     */
    public function addSubscriber(User $user = null): bool
    {
        if (!empty($user) && !$this->isUserSubscribed($user->id)) {
            $subscriber = new TaskSubscriber($this->w);
            $subscriber->task_id = $this->id;
            $subscriber->user_id = $user->id;
            $subscriber->insert();
            return true;
        } else {
            return false;
        }
    }

    public function addTaskGroupAsSubscribers()
    {
        $taskgroup = $this->getTaskGroup();
        if (!empty($taskgroup->id)) {
            // If automatic subscribe is ticked, assign all members as subscribers
            if ($taskgroup->shouldAutomaticallySubscribe()) {
                $members = $taskgroup->getMembers();
                if (!empty($members)) {
                    foreach ($members as $member) {
                        $member_user = AuthService::getInstance($this->w)->getUser($member->user_id);

                        if (!empty($member_user->id)) {
                            $this->addSubscriber($member_user);
                        }
                    }
                }
                // Else only assign the assignee and creator
            } else {
                $creator_id = $this->getTaskCreatorId();
                $this->addSubscriber(AuthService::getInstance($this->w)->getUser($creator_id));
                $this->addSubscriber(AuthService::getInstance($this->w)->getUser($this->assignee_id));
            }
        }
    }

    /**
     * Adds task type and task data to the index
     *
     * @return string
     */
    public function addToIndex()
    {
        $ttype = $this->getTaskTypeObject();
        $index = [];
        if ($ttype) {
            $index[] = $ttype->addToIndex($this);
        }

        $data = $this->getTaskData();
        if (!empty($data)) {
            foreach ($data as $d) {
                $index[] = $d->addToIndex();
            }
        }
        return implode(' ', $index);
    }

    public function __get($name)
    {
        // preload taskgroup if its called for
        if ($name === "_taskgroup") {
            $this->_taskgroup = $this->getTaskGroup();
            return $this->_taskgroup;
        } else {
            return parent::__get($name);
        }
    }

    public function isUrgent()
    {
        $taskgroup_type_object = TaskService::getInstance($this->w)->getTaskGroupTypeObject($this->_taskgroup->task_group_type);
        if (!empty($taskgroup_type_object->id)) {
            return $taskgroup_type_object->isUrgentPriority($this->priority);
        } else {
            return false;
        }
    }

    /**
     * Return a html string which will be displayed alongside
     * the generic task details.
     *
     */
    public function displayExtraDetails()
    {
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
    public function getTaskData()
    {
        return $this->getObjects("TaskData", ["task_id" => $this->id]);
    }

    /**
     * return the value of task data given the task ID and the key/name of the target attribute
     * task data is associated with the additional form fields available to a task type
     */
    public function getDataValue($key)
    {
        if ($this->id) {
            $c = TaskService::getInstance($this->w)->getObject("TaskData", ["task_id" => $this->id, "data_key" => $key]);
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
    public function setDataValue($key, $value)
    {
        if ($this->id) {
            $c = TaskService::getInstance($this->w)->getObject("TaskData", ["task_id" => $this->id, "data_key" => $key]);
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
    public function getCanIView(User $user = null)
    {
        if (empty($user)) {
            $user = AuthService::getInstance($this->w)->user();
        }

        if (empty($user->id)) {
            return false;
        }

        if ($user->is_admin == 1) {
            return true;
        }

        if ($user->hasRole("task_admin")) {
            return true;
        }

        $me = TaskService::getInstance($this->w)->getMemberGroupById($this->task_group_id, $user->id);

        if (empty($me)) {
            return false;
        }

        if ($user->id == $this->assignee_id) {
            return true;
        }

        if ($user->id == $this->getTaskCreatorId()) {
            return true;
        }

        $group = TaskService::getInstance($this->w)->getTaskGroup($this->task_group_id);
        return TaskService::getInstance($this->w)->getMyPerms($me->role, $group->can_view);
    }

    /**
     * used to hide the rate field
     * @return boolean
     */
    public function canISetRate()
    {
        $user = AuthService::getInstance($this->w)->User();
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
    public function canView(User $user)
    {
        return $this->getCanIView($user);
    }

    /**
     * Used by the search interface
     * @see DbObject::canList()
     */
    public function canList(User $user)
    {
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

    public function canDelete(\User $user)
    {
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
    public function getCanIEdit()
    {
        return $this->getCanIAssign();
    }

    // get my membership object and compare my role with that required to assigne tasks given a task group ID
    public function getCanIAssign()
    {
        if (AuthService::getInstance($this->w)->user()->is_admin == 1) {
            return true;
        }
        $me = TaskService::getInstance($this->w)->getMemberGroupById($this->task_group_id, $_SESSION['user_id']);
        $group = TaskService::getInstance($this->w)->getTaskGroup($this->task_group_id);

        return TaskService::getInstance($this->w)->getMyPerms($me->role, $group->can_assign);
    }

    // if i am assignee, creator or task group owner, i can set notifications for this Task
    public function getCanINotify()
    {
        if (AuthService::getInstance($this->w)->user()->is_admin == 1) {
            return true;
        }

        $logged_in_user_id = AuthService::getInstance($this->w)->user()->id;
        $me = TaskService::getInstance($this->w)->getMemberGroupById($this->task_group_id, $logged_in_user_id);

        if (($logged_in_user_id == $this->assignee_id) || ($logged_in_user_id == $this->getTaskCreatorId()) || (!empty($me->role) && TaskService::getInstance($this->w)->getMyPerms($me->role, "OWNER"))) {
            return true;
        }
        return false;
    }

    // return the ID of the task creator given a task ID
    public function getTaskCreatorId()
    {
        $c = TaskService::getInstance($this->w)->getObject("ObjectModification", ["object_id" => $this->id, "table_name" => $this->getDbTableName()]);
        return $c ? $c->creator_id : "";
    }

    // return the name for display of the task creator given a task ID
    public function getTaskCreatorName()
    {
        // I've moved the creator_id to tasks but this is for backwards compatability
        $creator = null;
        if (empty($this->creator_id)) {
            $c = TaskService::getInstance($this->w)->getObject("ObjectModification", ["object_id" => $this->id, "table_name" => $this->getDbTableName()]);
            if (!empty($c->creator_id)) {
                $creator = AuthService::getInstance($this->w)->getUser($c->creator_id);
            }
        } else {
            $creator = AuthService::getInstance($this->w)->getUser($this->creator_id);
        }

        return $creator ? $creator->getFullName() : "";
    }

    // return the task group title given a task group type
    public function getTypeTitle()
    {
        $c = TaskService::getInstance($this->w)->getTaskTypeObject($this->task_type);
        return (!empty($c) ? $c->getTaskTypeTitle() : null);
    }

    // return the task group description given the task group type
    public function getTypeDescription()
    {
        $c = TaskService::getInstance($this->w)->getTaskTypeObject($this->task_type);
        return (!empty($c) ? $c->getTaskTypeDescription() : null);
    }

    // return the task group title given a task group ID
    public function getTaskGroupTypeTitle()
    {
        return (!empty($this->_taskgroup->id) ? $this->_taskgroup->title : null);
    }

    // return the task types as array for a task group given a task group ID
    public function getTaskGroupTypes()
    {
        return (!empty($this->_taskgroup->id) ? $this->_taskgroup->getTypes() : null);
    }

    // return the task statuses as array for a task group given a task group ID
    public function getTaskGroupStatus()
    {
        return (!empty($this->_taskgroup->id) ? $this->_taskgroup->getTypeStatus() : null);
    }

    // status array has the form array(<status>,true|false);
    // get status types for a task group given a task group ID
    // given a status, return true| false ... $c[<status>] = true|false
    public function getisTaskClosed()
    {
        if ($this->is_closed !== null) {
            return $this->is_closed;
        }

        if (!empty($this->_taskgroup->id)) {
            $statlist = $this->_taskgroup->getStatus();
            if ($statlist) {
                foreach ($statlist as $stat) {
                    $status[$stat[0]] = $stat[1];
                }
                return (!empty($status[$this->status]) ? $status[$this->status] : null);
            }
        }
    }

    // return the task priorities as array given a task group ID
    public function getTaskGroupPriority()
    {
        return (!empty($this->_taskgroup->id) ? $this->_taskgroup->getPriority() : null);
    }

    // return list of time log entries for a task given task ID
    public function getTimeLogEntries($id = null)
    {
        if (empty($id)) {
            $id = $this->id;
        }
        return $this->getObjects("Timelog", ["object_class" => "Task", "object_id" => $id, "is_deleted" => 0]);
    }

    // return list of task time log entries, sort by start date
    public function getTimeLog()
    {
        $timelog = $this->getTimeLogEntries($this->id);

        if ($timelog) {
            usort($timelog, ["TaskService", "sortByStarted"]);
        }

        return $timelog;
    }

    // return due date in bold red for display, if it is on or past the due date
    public function isTaskLate()
    {
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
    public function getTaskTypeObject()
    {
        if ($this->task_type) {
            return TaskService::getInstance($this->w)->getTaskTypeObject($this->task_type);
        }
    }

    public function printSearchTitle()
    {
        $buf = (!empty($this->title) ? $this->title : 'Task [' . $this->id . ']') . ', ' . strtoupper($this->status);
        return $buf;
    }

    public function printSearchListing()
    {
        $tg = TaskService::getInstance($this->w)->getTaskGroup($this->task_group_id);
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

    public function printSearchUrl()
    {
        return "task/edit/" . $this->id;
    }

    public function toLink($class = null, $target = null, $user = null)
    {
        if (empty($user)) {
            $user = AuthService::getInstance($this->w)->user();
        }
        if ($this->canView($user)) {
            return Html::a($this->w->localUrl($this->printSearchUrl()), (!empty($this->title) ? htmlentities($this->title) : 'Task [' . $this->id . ']'), null, $class, null, $target);
        }
        return (!empty($this->title) ? htmlentities($this->title) : 'Task [' . $this->id . ']');
    }

    public function getAssignee()
    {
        if (!empty($this->assignee_id)) {
            return $this->getObject("User", $this->assignee_id);
        }
    }

    public function isStatusClosed()
    {
        $tg = $this->getTaskGroup();
        return $tg->isStatusClosed($this->status);
    }

    public function shouldAddToSearch()
    {
        if ($this->is_active) {
            return true;
        }
        return false;
    }

    /**
     * (non-PHPdoc)
     * @see DbObject::insert()
     */
    public function insert($force_validation = false)
    {
        try {
            $this->startTransaction();

            // 1. Call on_before_insert of the TaskGroupType

            $tg = $this->getTaskGroup();
            if (!empty($tg)) {
                if ($this->isStatusClosed()) {
                    $this->is_closed = 1;
                    // check dt_completed and set if empty
                    if (empty($this->dt_completed)) {
                        $this->dt_completed = formatDateTime(time());
                    }
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

            //check if assigned
            if (!empty($this->assignee_id)) {
                $user = AuthService::getInstance($this->w)->getUser($this->assignee_id);
                if (!empty($user->id)) {
                    // is assigned, check dt fields
                    if (empty($this->dt_assigned)) {
                        $this->dt_assigned = formatDateTime(time());
                    }
                    if (empty($this->dt_first_assigned)) {
                        $this->dt_first_assigned = formatDateTime(time());
                        $this->first_assignee_id = $this->assignee_id;
                    }
                }
            }

            //new task so set is_active to default value
            $this->is_active = 1;

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
                LogService::getInstance($this->w)->debug("Inserting Task: title is empty, calling update");
                $this->update();
            }

            // run any post-insert routines
            // add a comment upon task creation
            $comm = new TaskComment($this->w);
            $comm->obj_table = $this->getDbTableName();
            $comm->obj_id = $this->id;
            $comm->is_system = 1;
            $comm->is_internal = 1;
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
            LogService::getInstance($this->w)->error("Inserting Task: " . $ex->getMessage());
            $this->rollbackTransaction();
        }
    }

    /**
     * (non-PHPdoc)
     * @see DbObject::update()
     */
    public function update($force = false, $force_validation = false)
    {

        // 0. set the is_closed flag to make sure the task can be queried easily

        if ($this->isStatusClosed()) {
            $this->is_closed = 1;
            // check dt_completed and set if empty
            if (empty($this->dt_completed)) {
                $this->dt_completed = formatDateTime(time());
            }
        } else {
            $this->is_closed = 0;
        }

        //check if assigned and update dt fields
        if (!empty($this->assignee_id)) {
            $user = AuthService::getInstance($this->w)->getUser($this->assignee_id);
            if (!empty($user->id)) {
                // is assigned, check dt fields
                if (empty($this->dt_assigned) || $this->assignee_id != $this->__old['assignee_id']) {
                    $this->dt_assigned = formatDateTime(time());
                }
                if (empty($this->dt_first_assigned)) {
                    $this->dt_first_assigned = formatDateTime(time());
                    $this->first_assignee_id = $this->assignee_id;
                }
            }
        } else {
            // assignee_id cannot be null.
            $this->assignee_id = 0;
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
                LogService::getInstance($this->w)->error("Task update failed validation, rolling back transaction");
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

            //if not 'unassigned' add user to subscribers
            //check user exists
            $user = AuthService::getInstance($this->w)->getUser($this->assignee_id);
            $this->addSubscriber($user);

            $this->commitTransaction();
        } catch (Exception $ex) {
            LogService::getInstance($this->w)->error("Updating Task(" . $this->id . "): " . $ex->getMessage());
            $this->rollbackTransaction();
        }
    }

    /**
     * (non-PHPdoc)
     * @see DbObject::delete()
     */
    public function delete($force = false)
    {
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
            LogService::getInstance($this->w)->error("Deleting Task(" . $this->id . "): " . $ex->getMessage());
            $this->rollbackTransaction();
        }
    }

    /**
     * Checks if the user refered to by $user_id is subscribed to this task
     *  @return bool
     */
    public function isUserSubscribed(int $user_id): bool
    {
        $existing_subscription = $this->getObject('TaskSubscriber', ['task_id' => $this->id, 'user_id' => $user_id, 'is_deleted' => 0]);
        return !empty($existing_subscription->id);
    }

    public function getTaskGroup()
    {
        return TaskService::getInstance($this->w)->getTaskGroup($this->task_group_id);
    }

    public function getIcal()
    {
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
DTSTAMP:" . gmdate('Ymd') . 'T' . gmdate('His') . "Z
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

    public function getAttachmentsFileList($include_channel_email_raw = false)
    {
        $attachments = FileService::getInstance($this->w)->getAttachments($this);
        if (!empty($attachments)) {
            if (!$include_channel_email_raw) {
                $attachments = array_filter($attachments, function ($attachment) {
                    if ($attachment->type_code == 'channel_email_raw') {
                        return false;
                    }
                    return true;
                });
            }
            $pluck = [];
            foreach ($attachments as $attachment) {
                $file_path = $attachment->getFilePath();
                if ($file_path[strlen($file_path) - 1] !== '/') {
                    $file_path .= '/';
                }
                $pluck[] = realpath($file_path . $attachment->filename);
            }
            return $pluck;
        }
        return [];
    }
}
