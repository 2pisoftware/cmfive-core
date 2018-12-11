<?php

class TaskService extends DbService {

    public $_tasks_loaded;

    public function getSubscriber($subscriber_id) {
        return $this->getObject("TaskSubscriber", $subscriber_id);
    }

    public function getTaskGroupDetailsForUser() {
        $user_id = $this->w->Auth->user()->id;
		
		// Replacing functionality in favour of speed
		$member_of_task_groups = $this->_db->get("task_group_member")
                ->leftJoin("task_group on task_group.id = task_group_member.task_group_id")->select()->select("DISTINCT task_group.id")
                ->where("task_group_member.user_id", $user_id)->and("task_group_member.is_active", 1)
                ->and("task_group.is_active", 1)->and("task_group.is_deleted", 0)->fetchAll();
		
		$member_ids = [];
		if (!empty($member_of_task_groups)) {
			foreach ($member_of_task_groups as $member_of_task_group) {
				$member_ids[]  = $member_of_task_group["id"];
			}
		}
		
	
		$taskgroup_statuses = $this->w->db->get("task")->select()->select("DISTINCT status")
				->where("task.is_deleted", 0)->order_by("status ASC")->fetchAll();
		$statuses = [];
		
		if (!empty($taskgroup_statuses)) {
			foreach($taskgroup_statuses as $taskgroup_status) {
				$statuses[] = $taskgroup_status['status'];
			}
		}
		
		$taskgroup_priorities = $this->w->db->get("task")->select()->select("DISTINCT priority")
				->where("task.is_deleted", 0)->order_by("priority ASC")->fetchAll();
		$priorities = [];
		
		if (!empty($taskgroup_priorities)) {
			foreach($taskgroup_priorities as $taskgroup_priority) {
				$priorities[] = $taskgroup_priority['priority'];
			}
		}
		
		$taskgroup_tasktypes = $this->w->db->get("task")->select()->select("DISTINCT task_type")
				->where("task.is_deleted", 0)->order_by("task_type ASC")->fetchAll();
		$tasktypes = [];
		
		if (!empty($taskgroup_tasktypes)) {
			foreach($taskgroup_tasktypes as $taskgroup_tasktype) {
				$tasktypes[] = $taskgroup_tasktype['task_type'];
			}
		}
		
		$members = $this->w->db->get("task_group_member")->select()->select("DISTINCT task_group_member.user_id")->fetchAll(); 
		
		$flat_members = [];
		if (!empty($members)) {
			foreach($members as $member) {
				$flat_members[] = $member['user_id'];
			}
		}
		
		$taskgroup_members = [];
		if (!empty($flat_members)) {
			$taskgroup_members = $this->getObjects("User", ["id" => $flat_members]);

			uasort($taskgroup_members, function($a, $b) {
				return strcmp($a->getFullName(), $b->getFullName());
			});
		}
		
		return ["statuses" => $statuses, "priorities" => $priorities, "members" => $taskgroup_members, "types" => $tasktypes];
    }

    public function getTaskGroupDetailsForTaskGroup($taskgroup_id) {
    	$taskgroup = $this->Task->getTaskGroup($taskgroup_id);
    
    	$taskgroup_details = array("taskgroups" => array(), "statuses" => array(), "priorities" => array(), "members" => array(), "types" => array());
    	if (!empty($taskgroup)) {
    			$taskgroup_details["taskgroups"][] = $taskgroup;
    			$taskgroup_details["statuses"] = $taskgroup->getStatus();
    			$taskgroup_details["priorities"] = $taskgroup->getPriority();
    			$taskgroup_details["members"] = $this->getMembersInGroup($taskgroup->id);
    			$task_type_array = $taskgroup->getTaskGroupTypeObject()->getTaskTypeArray();
    			$taskgroup_details["types"][key($task_type_array)] = array($task_type_array[key($task_type_array)], key($task_type_array));
    	}
    
    	return $taskgroup_details;
    }
    
    public function flattenTaskGroupArray($statuses) {
        $result_array = array();
        if (!empty($statuses)) {
            foreach($statuses as $status) {
                if (!is_bool($status[1])) {
                    $result_array[$status[1]] = $status[0];
                } else {
                    $result_array[] = $status[0];
                }
            }
        }
        
        return $result_array;
    }
    
    public function getSelectArrayForTaskGroupType($class) {
        $taskgroup_type_object = $this->getTaskGroupTypeObject($class);
        if (!empty($taskgroup_type_object)) {
            $status_array = $taskgroup_type_object->getStatusArray();
            return $this->flattenTaskGroupArray($status_array);
        }
        return null;
    }
    
    // function to sort lists by date created
    static function sortByCreated($a, $b) {
        if ($a->dt_created == $b->dt_created) {
            return 0;
        }
        return ($a->dt_created < $b->dt_created) ? +1 : -1;
    }

    // function to sort task time log by date started
    static function sortByStarted($a, $b) {
        if ($a->dt_start == $b->dt_start) {
            return 0;
        }
        return ($a->dt_start > $b->dt_start) ? +1 : -1;
    }

    // function to sort task group list by task type
    static function sortbyGroup($a, $b) {
        if (strcasecmp($a->task_group_type, $b->task_group_type) == 0) {
            return 0;
        }
        return (strcasecmp($a->task_group_type, $b->task_group_type) > 0) ? +1 : -1;
    }

    // function to sort task lists by due date
    static function sortTasksbyDue($a, $b) {
        if ($a->dt_due == $b->dt_due) {
            return 0;
        }
        return ($a->dt_due > $b->dt_due) ? +1 : -1;
    }

    // function to sort groups lists by users role
    static function sortbyRole($a, $b) {
        if ($a->role == $b->role) {
            return 0;
        }
        return ($a->role > $b->role) ? +1 : -1;
    }

    // convert dd/mm/yyyy date to yyy-mm-dd for SQL statements
    function date2db($date) {
        if ($date) {
            list($d, $m, $y) = preg_split("/\/|-|\./", $date);
            return $y . "-" . $m . "-" . $d;
        }
    }

    // nicely format a number of seconds as H:m
    function getFormatPeriod($seconds) {
        if (is_numeric($seconds)) {
            $hours = intval($seconds / 3600);
            $mins = intval(($seconds / 60) % 60);
            $mins = str_pad($mins, 2, "0", STR_PAD_LEFT);
            return $hours . ":" . $mins;
        }
    }

    // return a date one week behind 'today'
    function getLastWeek() {
        $cd = strtotime(date("Y-m-d"));
        $newdate = date("d/m/Y", mktime(0, 0, 0, date("m", $cd), date("d", $cd) - 7, date("Y", $cd)));
        return $newdate;
    }

    // return a date one month in advance of 'today'
    function getNextMonth() {
        $cd = strtotime(date("Y-m-d"));
        $newdate = date("d/m/Y", mktime(0, 0, 0, date("m", $cd) + 1, date("d", $cd), date("Y", $cd)));
        return $newdate;
    }

    // mark up URLS as <a> links
    function findURL($text) {
        if ($text != "") {
            // decode text back to HTML entities
            $text = htmlspecialchars_decode($text);
            // find URLs - may be more than one
            preg_match_all("/https?:\/\/[a-zA-Z0-9\.\/\?&=\-\%_\+]*/", $text, $urls);

            if ($urls) {
                foreach ($urls as $url) {
                    foreach ($url as $u) {
                        // foreach URL create a marker to replace the URL.
                        // create array, key: marker + value: marked-up URL.
                        // use RAND to improve uniqueness of marker in the text so no accidental string substitutions
                        $marker = "URLMARKER" . rand(100, 999);
                        $newurl = "<a href=\"" . $u . "\" target=\"_blank\">" . $u . "</a>";
                        $mark[$marker] = $newurl;
                        $text = str_replace($u, $marker, $text);
                    }
                }
                // again encode the text
                $text = htmlspecialchars($text);

                // replace the markers in the encoded text with unencoded URLs
                if (!empty($mark)) {
                    foreach ($mark as $marker => $url) {
                        $text = str_replace($marker, $url, $text);
                    }
                }
            }
        }
        // return the text
        return str_replace("\r\n", "<br>", $text);
    }

    // get a task group from the database by its ID
    function getTaskGroup($id) {
        return $this->getObject("TaskGroup", $id);
    }

    // get all active task groups from the database
    function getTaskGroups($include_inactive = false) {
        $where = ["is_deleted" => 0];
        if (!$include_inactive) {
            $where['is_active'] = 1;
        }
        return $this->getObjects("TaskGroup", $where);
    }

    // get all task groups from the database of given task group type
    function getTaskGroupsByType($id) {
        return $this->getObjects("TaskGroup", array("is_active" => 1, "is_deleted" => 0, "task_group_type" => $id));
    }

    // get all task group types as defined in our tasks file
    function getAllTaskGroupTypes() {
        $this->_loadTaskFiles();
        foreach (get_declared_classes() as $class) {
            if (startsWith($class, "TaskGroupType_")) {
                $tgt = new $class($this->w);
                $taskgrouptypes[] = array($tgt->getTaskGroupTypeTitle(), $class);
            }
        }
        return $taskgrouptypes;
    }

    // prepare to get all task groups of type $class as defined in our tasks file
    function getTaskGroupTypeObject($class) {
        return $this->_getTaskObjectGeneric($class, "TaskGroupType_");
    }

    // prepare to get all task types of type $class as defined in our tasks file
    function getTaskTypeObject($class) {
        return $this->_getTaskObjectGeneric($class, "TaskType_");
    }

    // get all task groups or task types of type $class as defined in our task file
    function _getTaskObjectGeneric($class, $type) {
        $this->_loadTaskFiles();
        $class = startsWith($class, $type) ? $class : $type . $class;
        if (class_exists($class)) {
            return new $class($this->w);
        }
        return null;
    }

    // return the task group type by a task group ID
    function getTaskGroupTypeById($id) {
        $c = $this->getTaskGroup($id);
        if (!empty($c->id)) {
            return $c->task_group_type;
        }
        return null;
    }

    // return the task group type by a task group ID
    function getTaskGroupTitleById($id) {
        $c = $this->getTaskGroup($id);
        return $c->title;
    }

    // return the task group description as defined in our tasks file for a given type/class
    function getTaskGroupDescription($class) {
        $this->_loadTaskFiles();
        $tgt = new $class($this->w);
        return $tgt->getTaskGroupTypeDescription();
    }

    // return the task group flag, re: can tasks be reopened as defined in our tasks file for a given type/class
    function getCanTaskReopen($taskgroup) {
        $this->_loadTaskFiles();
        if (class_exists($taskgroup)) {
            $c = new $taskgroup($this->w);
            return $c->getCanTaskGroupReopen();
        }
        return false;
    }

    // return user notify record given task ID, user id
    function getTaskUserNotify($id, $tid) {
        return $this->getObject("TaskUserNotify", array("user_id" => $id, "task_id" => $tid));
    }

    // return all notify records given user id and taskgroup ID
    function getTaskGroupUserNotify($id, $tid) {
        return $this->getObjects("TaskGroupUserNotify", array("user_id" => $id, "task_group_id" => $tid));
    }

    // return notify record for user given user id, taskgroup ID, role and type
    function getTaskGroupUserNotifyType($id, $tid, $role, $type) {
        return $this->getObject("TaskGroupUserNotify", array("user_id" => $id, "task_group_id" => $tid, "role" => $role, "type" => $type));
    }

    // return the recordset of notify matrix for given Task Group
    function getTaskGroupNotify($id) {
        return $this->getObjects("TaskGroupNotify", array("task_group_id" => $id));
    }

    // return notify record for Task Group given taskgroup ID, role and type
    function getTaskGroupNotifyType($id, $role, $type) {
        return $this->getObject("TaskGroupNotify", array("task_group_id" => $id, "role" => $role, "type" => $type));
    }

    // static list of group permissions for can_view, can_assign, can_create
    function getTaskGroupPermissions() {
        return array("ALL", "GUEST", "MEMBER", "OWNER");
    }

    function getTaskGroupRoles() {
        return array("GUEST", "MEMBER", "OWNER");
    }

    // determine if current user can perform a task
    // compare users role against required role to perform given task
    function getMyPerms($role, $required_permission) {
        $permissions = $this->getTaskGroupPermissions();

        // key = permission level, value = ascending number
        $i = 0;
        $permission_array = array();
        foreach ($permissions as $permission) {
            $permission_array[$permission] = $i++;
        }

        // if number of user role is >= number of requesite level, then allow
        if (!empty($permission_array[$role]) && !empty($permission_array[$required_permission])){
            if ($permission_array[$role] >= $permission_array[$required_permission]) {
                return true;
            }
        }
        return false;
    }

    public function getTasks($where = array()) {
        $where["is_deleted"] = 0;
        
        return $this->getObjects("Task", $where);
    }
    
    // return a task group from the database given its ID
    function getTasksbyGroupId($id) {
        $where = ($id) ? array("task_group_id" => $id) : null;
        return $this->getObjects("Task", $where);
    }

    // given a where clause, return all tasks created by a given user ID
    // required to join with modifiable aspect to determine task creator
    function getCreatorTasks($id, $clause = null) {
        if (is_array($clause)) {
            foreach ($clause as $name => $value) {
                $where .= "and t." . $name . " = '" . $value . "' ";
            }
        } elseif ($clause != "") {
            $where = " and " . $clause;
        }
        $where .= " and t.is_deleted = 0 and g.is_active = 1 and g.is_deleted = 0";

        // check that task group is active and not deleted
        $rows = $this->_db->sql("SELECT t.* from " . Task::$_db_table . " as t inner join " . ObjectModification::$_db_table . " as o on t.id = o.object_id inner join " . TaskGroup::$_db_table . " as g on t.task_group_id = g.id where o.creator_id = " . $id . " and o.table_name = '" . Task::$_db_table . "' " . $where . " order by t.id")->fetch_all();
        $rows = $this->fillObjects("Task", $rows);
        return $rows;
    }

    // return all resulting tasks from the database modified in the last week
    function getTaskWeek($group, $assignee, $from, $to) {
        $grps = $who = $grplist = "";

        // if no group supplied, get all my groups
        if ($group != "") {
            $grplist = $group;
        } else {
            // list the groups i am a member of
            $groups = $this->getMemberGroups($_SESSION['user_id']);
            if ($groups) {
                foreach ($groups as $group) {
                    $grplist .= $group->task_group_id . ",";
                }
            } else {
                return null;
            }
            $grplist = rtrim($grplist, ",");
        }

        if ($assignee != "")
            $who = " c.creator_id = " . $assignee . " and ";

        // create where clause giving all active tasks which have shown activity in the last week
        // need to check if task group is deleted
        $grps = "t.task_group_id in (" . $grplist . ") and ";
        $where = "where " . $grps . $who . " t.is_deleted = 0 and g.is_active = 1 and g.is_deleted = 0";
        $where .= " and date_format(c.dt_modified,'%Y-%m-%d') >= '" . $this->date2db($from) . "' and date_format(c.dt_modified,'%Y-%m-%d') <= '" . $this->date2db($to) . "'";

        // get and return tasks
        $rows = $this->_db->sql("SELECT t.id, t.title, t.task_group_id, c.comment, c.creator_id, c.dt_modified from " . Task::$_db_table . " as t inner join " . TaskComment::$_db_table . " as c on t.id = c.obj_id and c.obj_table = '" . Task::$_db_table . "' inner join " . TaskGroup::$_db_table . " as g on t.task_group_id = g.id " . $where . " order by c.dt_modified desc")->fetch_all();
        return $rows;
    }

    // get a task from the database given its ID
    function getTask($id) {
        return $this->getObject("Task", $id);
    }

    // get the task data from the database given a task ID
    function getTaskData($id) {
        return $this->getObjects("TaskData", array("task_id" => $id));
    }

    function getTaskByTaskDataKeyValuePair($key, $value) {
        $taskdata = $this->Task->getObject("TaskData", array("data_key" => $key, "value" => $value));
        if (!empty($taskdata->id)) {
            return $this->getTask($taskdata->task_id);
        }
        return null;
    }

    // return an array for display of task type for a task group defined in our tasks file.
    function getTaskTypes($taskgroup) {
        if (empty($taskgroup)) {
            return null;
        }
        
        $tasktypes = array();
        $this->_loadTaskFiles();
        foreach (get_declared_classes() as $class) {
            if ($class == $taskgroup) {
                $tgt = new $class($this->w);
                foreach ($tgt->getTaskTypeArray() as $short_tasktype => $long_tasktype) {
                    $tasktypes[] = array($long_tasktype, $short_tasktype);
                }
            }
        }
        return $tasktypes;
    }

    // returns an array of statuses of a task group defined in our tasks file
    function getTaskStatus($taskgroup) {
        $this->_loadTaskFiles();
        if (is_string($taskgroup) && class_exists($taskgroup)) {
            $c = new $taskgroup($this->w);
            if (is_a($c, "TaskGroupType")) {
                return $c->getStatusArray();
            }
        }
    }

    // returns an array for display of statuses of a task group defined in our tasks file
    function getTaskTypeStatus($taskgroup) {
        $this->_loadTaskFiles();
        $arrstatus = $this->getTaskStatus($taskgroup);
        if ($arrstatus) {
            foreach ($arrstatus as $status) {
                $statuses[] = array($status[0], $status[0]);
            }
            return $statuses;
        }
    }

    // returns an array for display of priorities of a task group defined in our tasks file
    function getTaskPriority($taskgroup) {
        $taskprior = array();
        $this->_loadTaskFiles();
        if (class_exists($taskgroup)) {
            $tgt = new $taskgroup($this->w);
            if (is_a($tgt, "TaskGroupType")) {
                $priority = $tgt->getTaskPriorityArray();
                foreach ($priority as $taskpriority) {
                    $taskprior[] = array($taskpriority, $taskpriority);
                }
            }
        }
        return $taskprior;
    }

    // returns the additional form fields for a task type as defined in our task file
    function getFormFieldsByTask($tasktype, TaskGroup $tg) {
        $fieldform = array();
        $this->_loadTaskFiles();
        $fieldform = null;
        foreach (get_declared_classes() as $class) {
            if (startsWith($class, "TaskType_" . $tasktype)) {
                $tgt = new $class($this->w);
                $fieldform = $tgt->getFieldFormArray($tg);
            }
        }
        return $fieldform;
    }

    // return a task comment by the COMMENT ID
    function getComment($id) {
        return $this->w->Auth->getObject("TaskComment", array("obj_table" => Task::$_db_table, "id" => $id));
    }

//    function getTaskTimes() {
//        return $this->getObjects("TaskTime", array("is_deleted" => 0, "user_id" => $this->w->Auth->user()->id));
//    }
    
    // return a time log entry by log entry ID
//    function getTimeLogEntry($id) {
//        return $this->getObject("TaskTime", array("id" => $id, "is_deleted" => 0));
//    }

    // return an array of the owners of a task group from the database
    function getTaskGroupOwners($id) {
        return $this->getObjects("TaskGroupMember", array("task_group_id" => $id, "role" => "OWNER", "is_active" => 1));
    }

    // determine if a given user is an owner of a task group.
    // input: task group ID & user ID
    function getIsOwner($task_group_id, $user_id) {
        $owners = $this->getTaskGroupOwners($task_group_id);
        if ($owners) {
            foreach ($owners as $owner) {
                if ($owner->user_id == $user_id)
                    return true;
            }
        }
        return false;
    }

    // return all groups from the database of which a user is a member, given user ID. else, return all groups
    function getMemberGroups($id = null) {
        if (empty($id)) {
            return null;
        }
        
        $query = $this->_db->get("task_group_member")
                ->leftJoin("task_group")
                ->where("task_group_member.user_id", $id)->and("task_group_member.is_active", 1)
                ->and("task_group.is_active", 1)->and("task_group.is_deleted", 0);
        return $this->getObjectsFromRows("TaskGroupMember", $query->fetch_all());
    }

    function getTaskGroupsForMember($id = null) {
        if (empty($id)) {
            return null;
        }
        
        $query = $this->_db->get("task_group_member")
                ->leftJoin("task_group")->select("task_group.*")
                ->where("task_group_member.user_id", $id)->and("task_group_member.is_active", 1)
                ->and("task_group.is_active", 1)->and("task_group.is_deleted", 0);
        return $this->getObjectsFromRows("TaskGroup", $query->fetch_all());
    }
    
    // return all members of a task group from the database, given the task group ID
    function getMemberGroup($id) {
        return $this->getObjects("TaskGroupMember", array("task_group_id" => $id, "is_active" => 1));
    }

    // return an array for display of all members in a given task group, by task group ID
    function getMembersInGroup($id) {
		$line = [];
        $members = $this->getObjects("TaskGroupMember", array("task_group_id" => $id, "is_active" => 1));
		if (!empty($members)) {
			foreach ($members as $member) {
				$line[] = array($this->getUserById($member->user_id), $member->user_id);
			}
		}
        return $line;
    }

    // return an array for display of all members of a task group who can be assigned tasks, given task group ID
    function getMembersBeAssigned($id) {
        $line = array();
        $where = "task_group_id = " . $id . " and (role = 'MEMBER' or role = 'OWNER') and is_active = 1";
        $members = $this->getObjects("TaskGroupMember", $where);
        if (!empty($members)) {
            foreach ($members as $member) {
                $line[] = array($this->getUserById($member->user_id), $member->user_id);
            }
        }
        return $line;
    }

    // return a member object given the task_group_member database ID: targets specific member in specific task group
    function getMemberById($id) {
        return $this->getObject("TaskGroupMember", array("id" => $id));
    }

    // return a member object given a task group ID and a user ID
    function getMemberGroupById($group, $uid) {
        return $this->getObject("TaskGroupMember", array("task_group_id" => $group, "user_id" => $uid, "is_active" => 1));
    }

    // return a users full name given their user ID
    function getUserById($id) {
        $u = $this->w->Auth->getUser($id);
        return $u ? $u->getFullName() : "";
    }

    // load our task files to make available: titles, descriptions, status, additional form fields, etc.
    // for defined task groups amd task types
    function _loadTaskFiles() {
        // do this only once
        if ($this->_tasks_loaded)
            return;

        $handlers = $this->w->modules();
        foreach ($handlers as $model) {
            $file = $this->w->getModuleDir($model) . $model . ".tasks.php";
            if (file_exists($file)) {
                require_once $file;
            }
        }
        $this->_tasks_loaded = true;
    }

    function getTaskGroupByUniqueTitle($title) {
        return $this->getObject("TaskGroup", array("title" => $title, "is_deleted" => 0));
    }

    function addMemberToTaskGroup($taskgroup_id, $user_id, $role = "GUEST") {
        if (empty($taskgroup_id) || empty($user_id))
            return;

        // Check that they're not already a member
        $member = $this->getObject("TaskGroupMember", array("task_group_id" => $taskgroup_id, "user_id" => $user_id));
        if (!empty($member))
            return;

        $taskgroupmember = new TaskGroupMember($this->w);
        $taskgroupmember->task_group_id = $taskgroup_id;
        $taskgroupmember->user_id = $user_id;
        $taskgroupmember->role = $role;
        $taskgroupmember->is_active = 1;
        $taskgroupmember->insert();
    }

    function removeMemberFromTaskGroup($taskgroup_id, $user_id) {
    	$tgm = $this->getObject("TaskGroupMember", array("task_group_id"=>$taskgroup_id, "user_id"=>$user_id));
    	if (!empty($tgm)) {
    		$tgm->delete();
    	}
    }
    
    /**
     * Create a new Task
     * 
     * @param unknown $task_type
     * @param unknown $task_group_id
     * @param unknown $title
     * @param unknown $description
     * @param unknown $priority
     * @param unknown $dt_due
     * @param unknown $first_assignee_id
     */
    function createTask($task_type, $task_group_id, $title, $description, $priority, $dt_due, $first_assignee_id) {
        $task = new Task($this->w);
        $task->task_type = $task_type;
        $task->task_group_id = $task_group_id;
        $task->title = $title;
        $task->description = $description;
        $task->priority = $priority;
        $task->dt_due = $dt_due;
        $task->first_assignee_id = $first_assignee_id;
        $task->assignee_id = $first_assignee_id;

        $task->insert();
        return $task;
    }

    /**
     * Create a new Taskgroup using all the form details of the taskgroup form
     * 
     * @param task_group_type, eg. "TaskGroupType_TaskTodo"
     * @param title, the task group title
     * @param can_assign, OWNER|MEMBER|GUEST
     * @param can_view, OWNER|MEMBER|GUEST
     * @param can_create, OWNER|MEMBER|GUEST
     * @param is_active, 0|1
     * @param is_deleted, 0|1
     * @param description, a description
     * @param default_assignee_id, a user_id or null
     *  
     * @return TaskGroup
     */
    function createTaskGroup($type, $title, $description, $default_assignee_id, $can_assign = "OWNER", $can_view = "OWNER", $can_create = "OWNER", $is_active = 1, $is_deleted = 0, $default_task_type = null, $default_priority = null, $is_automatic_subscription = false) {
        // title should be unique!
        $taskgroup = $this->getTaskGroupByUniqueTitle($title);
        if (null != $taskgroup) {
            return $taskgroup;
        }

        // insert newly created task group into the task_group database
        $taskgroup = new TaskGroup($this->w);
        $taskgroup->task_group_type = $type;
        $taskgroup->title = $title;
        $taskgroup->description = $description;
        $taskgroup->can_assign = $can_assign;
        $taskgroup->can_view = $can_view;
        $taskgroup->can_create = $can_create;
        $taskgroup->is_active = $is_active;
        $taskgroup->is_deleted = !empty($is_deleted) ? $is_deleted : 0;
        $taskgroup->default_assignee_id = $default_assignee_id;
        $taskgroup->default_task_type = $default_task_type;
        $taskgroup->default_priority = $default_priority;
		$taskgroup->is_automatic_subscription = !!$is_automatic_subscription;
        $response = $taskgroup->insert();
        
        // Check the validation
        if ($response !== true) {
            $this->w->errorMessage($taskgroup, "Taskgroup", $response, false, "/task-group/viewtaskgrouptypes#create");
        }
        
        // if created succcessfully, create default notify matrix: all on
        if ($taskgroup->id) {
            $arr['guest']['creator'] = 1;
            $arr['member']['creator'] = 1;
            $arr['member']['assignee'] = 1;
            $arr['owner']['creator'] = 1;
            $arr['owner']['assignee'] = 1;
            $arr['owner']['other'] = 1;

            // so foreach role/type lets put the values in the database
            foreach ($arr as $role => $types) {
                foreach ($types as $type => $value) {
                    $notify = new TaskGroupNotify($this->w);
                    $notify->task_group_id = $taskgroup->id;
                    $notify->role = $role;
                    $notify->type = $type;
                    $notify->value = $value;
                    $notify->insert();
                }
            }
        }

        // if task group is successfully created and a default assignee is defined
        // create a task group membership list and set this person as the task group owner
        // if no default assignee, a task group membership list can be created at any time
        if (($taskgroup->id) && ($default_assignee_id != "")) {
            $arrdb = array();
            $arrdb['task_group_id'] = $taskgroup->id;
            $arrdb['user_id'] = $default_assignee_id;
            $arrdb['role'] = "OWNER";
            $arrdb['priority'] = 1;
            $arrdb['is_active'] = 1;

            $mem = new TaskGroupMember($this->w);
            $mem->fill($arrdb);
            $mem->insert();
        }

        return $taskgroup;
    }

    public function getNotifyUsersForTask($task, $event) {
        if (empty($task)) {
            return array();
        }
        
        $me = array();
        // This may be called from cron
        if (!empty($_SESSION['user_id'])) {
            $me = array($this->getMemberGroupById($task->task_group_id, $_SESSION['user_id']));
        }
        
        // get member object for task creator
        $creator_id = $task->getTaskCreatorId();
		
		// Notify assignee too
        $creator = array($this->getMemberGroupById($task->task_group_id, $creator_id), !empty($task->assignee_id) ? $this->getMemberGroupById($task->task_group_id, $task->assignee_id) : null);
        // get member object(s) for task group owner(s)
        $owners = $this->getTaskGroupOwners($task->task_group_id);

        // us is everyone
		if (empty($owners) || !is_array($owners)) {
			$owners = [];
		}
        $us = (object) array_merge($me, $creator, $owners);

        if (empty($us)) {
            return array();
        }
        
        $notifyUsers = array();
        
        // foreach relavent member
        foreach ($us as $i) {
            if (empty($i)) {
                continue;
            }
            
            // set default notification value. 0 = no notification
            $shouldNotify = false; // $value = "0";
            // set current user's role
            $role = strtolower($i->role);
            // determine current user's 'type' for this task
            $assignee = ($task->assignee_id == $i->user_id);
            $creator = ($creator_id == $i->user_id);
            $owner = $this->getIsOwner($task->task_group_id, $i->user_id);

            // this user may be any or all of the 'types'
            // need to check each 'type' for a notification
            $types = array();
            if (!empty($assignee)) {
                $types[] = "assignee";
            }
            if (!empty($creator)) {
                $types[] = "creator";
            }
            if (!empty($owner)) {
                $types[] = "other";
            }

            // if they have a type ... look for notifications
            if (!empty($types)) {
                // check user task notifications
                $notify = $this->getTaskUserNotify($i->user_id, $task->id);
                // if there is a record, get notification flag
                if (!empty($notify)) {
                    $shouldNotify = (bool) $notify->$event; // $value = $notify->$event;
                }
                // if no user task notification present, check user task group notification for role and type
                else {

                    // for each type, check the User defined notification table 
                    foreach ($types as $type) {
                        $notify = $this->getTaskGroupUserNotifyType($i->user_id, $task->task_group_id, $role, $type);
                        // if there is a notification flag and it equals 1, no need to go further, a notification will be sent
                        if (!empty($notify)) {
                            if ($notify->value == "1") {
                                $shouldNotify = (bool) $notify->$event;
                                break;
                            }
                        }
                    }
                }

                // if no user task group notification present, check task group default notification for role and type
                if (empty($notify)) {
                    foreach ($types as $type) {
                        $notify = $this->getTaskGroupNotifyType($task->task_group_id, $role, $type);
                        // if notification exists, set its value
                        if (!empty($notify)) {
                            $shouldNotify = (bool) $notify->value;
                        }
                        // if its value is 1, no need to go further, a notification will be sent
                        if ($shouldNotify) {
                            break;
                        }
                    }
                }
                // if somewhere we have found a positive notification, add user_id to our send list
                if ($shouldNotify) {
                    $notifyUsers[$i->user_id] = $i->user_id;
                }
            }
            unset($types);
        }
        return $notifyUsers;
    }
    
	public function getNotificationAdditionalDetails(Task $task) {
		$additional_details = $this->w->callHook("task", "notification_additional_details", $task);
		$message = '';
		
		if (!empty($additional_details)) {
//			$message .= "<br/><p>Additional details:</p>";
			foreach($additional_details as $additional_detail) {
				if (!empty($additional_detail)) {
					$message .= "<p>" . $additional_detail . "</p>";
				}
			}
		}
		
		return !empty($message) ? "<br/><p>Additional details:</p>" . $message : '';
	}
    
    public function navigation(Web $w, $title = null, $nav = null) {
        if ($title) {
            $w->ctx("title", $title);
        }

        $nav = $nav ? $nav : array();

        if ($w->Auth->loggedIn()) {
            $w->menuLink("task/index", "Task Dashboard", $nav);
            $w->menuLink("task/edit", "New Task", $nav);
//          $w->menuLink("task/index", "Task Dashboard", $nav);
            $w->menuLink("task/tasklist", "Task List", $nav);
            $w->menuLink("task/notifications", "Notifications", $nav);
            $w->menuLink("task/taskweek", "Activity", $nav);
            $w->menuLink("task-group/viewtaskgrouptypes", "Task Groups", $nav);
        }
        $w->ctx("navigation", $nav);
        return $nav;
    }

}
