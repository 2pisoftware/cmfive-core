<?php

class TaskService extends DbService {

    /**
     * Get a list of tasks
     * 
     * @param Array
     * @return Array|null
     */
    public function getTasks($where = []) {
        return $this->getObjects("Task", $where);
    }

    /**
     * Get a task
     * 
     * @param int
     * @return Task|null
     */
    public function getTask($id) {
        return $this->getObject("Task", $id);
    }

    /**
     * Get a list of task groups
     * 
     * @param Array
     * @return Array|null
     */
    public function getTaskGroups($where = []) {
        return $this->getObjects("TaskGroup", $where);
    }

    /**
     * Get a taskgroup by ID
     * 
     * @param int
     * @return TaskGroup|null
     */
    public function getTaskGroup($id) {
        return $this->getObject("TaskGroup", $id);
    }

    // List filter functions
    public function getCreators() {
        return $this->w->db->query("select distinct om.creator_id, concat(c.firstname, ' ', c.lastname) as fullname from object_modification om inner join user u on u.id = om.creator_id inner join contact c on u.contact_id = c.id where om.table_name = 'task';")->fetchAll();
    }

    public function getAssignees() {
        return $this->w->db->query("select distinct t.assignee_id, concat(c.firstname, ' ', c.lastname) as fullname from task t inner join `user` u on u.id = t.assignee_id inner join contact c on u.contact_id = c.id;")->fetchAll();
    }

    public function getTaskTypesList() {
        return $this->w->db->get("task")->select()->select("DISTINCT task_type")->fetchAll();
    }

    public function getPriorityList() {
        return $this->w->db->query("select distinct priority from task where priority != '' and priority != null")->fetchAll();
    }

    public function getStatusList() {
        return $this->w->db->get("task")->select()->select("DISTINCT status")->fetchAll();
    }








//     // Old task service
// 	private $_tasks_loaded = false;

// 	public function __construct($w) {
//         parent::__construct($w);
        
//         $this->_loadTaskFiles();
//     }

//     public function getTaskGroupDetailsForUser() {
//         $user_id = $this->w->Auth->user()->id;

// 		// Replacing functionality in favour of speed
// 		$member_of_task_groups = $this->_db->get("task_group_member")
//                 ->leftJoin("task_group on task_group.id = task_group_member.task_group_id")->select()->select("DISTINCT task_group.id")
//                 ->where("task_group_member.user_id", $user_id)->and("task_group_member.is_active", 1)
//                 ->and("task_group.is_active", 1)->and("task_group.is_deleted", 0)->fetchAll();

// 		$member_ids = [];
// 		if (!empty($member_of_task_groups)) {
// 			foreach ($member_of_task_groups as $member_of_task_group) {
// 				$member_ids[]  = $member_of_task_group["id"];
// 			}
// 		}


// 		$taskgroup_statuses = $this->w->db->get("task")->select()->select("DISTINCT status")
// 				->where("task.is_deleted", 0)->order_by("status ASC")->fetchAll();
// 		$statuses = [];

// 		if (!empty($taskgroup_statuses)) {
// 			foreach($taskgroup_statuses as $taskgroup_status) {
// 				$statuses[] = $taskgroup_status['status'];
// 			}
// 		}

// 		$taskgroup_priorities = $this->w->db->get("task")->select()->select("DISTINCT priority")
// 				->where("task.is_deleted", 0)->order_by("priority ASC")->fetchAll();
// 		$priorities = [];

// 		if (!empty($taskgroup_priorities)) {
// 			foreach($taskgroup_priorities as $taskgroup_priority) {
// 				$priorities[] = $taskgroup_priority['priority'];
// 			}
// 		}

// 		$taskgroup_tasktypes = $this->w->db->get("task")->select()->select("DISTINCT task_type")
// 				->where("task.is_deleted", 0)->order_by("task_type ASC")->fetchAll();
// 		$tasktypes = [];

// 		if (!empty($taskgroup_tasktypes)) {
// 			foreach($taskgroup_tasktypes as $taskgroup_tasktype) {
// 				$tasktypes[] = $taskgroup_tasktype['task_type'];
// 			}
// 		}

// 		$members = $this->w->db->get("task_group_member")->select()->select("DISTINCT task_group_member.user_id")->fetchAll();

// 		$flat_members = [];
// 		if (!empty($members)) {
// 			foreach($members as $member) {
// 				$flat_members[] = $member['user_id'];
// 			}
// 		}

// 		$taskgroup_members = [];
// 		if (!empty($flat_members)) {
//             $taskgroup_members = $this->getObjects("User", ["id" => $flat_members]);
//         }
// 		$this->_loadTaskFiles();
// 	}

// 		// 	uasort($taskgroup_members, function($a, $b) {
// 		// 		return strcmp($a->getFullName(), $b->getFullName());
// 		// 	});
// 		// }

//         // return ["statuses" => $statuses, "priorities" => $priorities, "members" => $taskgroup_members, "types" => $tasktypes];
        
//     public function getTaskGroups() {
//         return $this->getObjects('TaskGroup', ['is_active' => 1, 'is_deleted' => 0]);
//     }

//     public function getTaskGroupDetailsForTaskGroup($taskgroup_id) {
//     	$taskgroup = $this->Task->getTaskGroup($taskgroup_id);

//     	$taskgroup_details = array("taskgroups" => array(), "statuses" => array(), "priorities" => array(), "members" => array(), "types" => array());
//     	if (!empty($taskgroup)) {
//     			$taskgroup_details["taskgroups"][] = $taskgroup;
//     			$taskgroup_details["statuses"] = $taskgroup->getStatus();
//     			$taskgroup_details["priorities"] = $taskgroup->getPriority();
//     			$taskgroup_details["members"] = $this->getMembersInGroup($taskgroup->id);
//     			$task_type_array = $taskgroup->getTaskGroupTypeObject()->getTaskTypeArray();
//     			$taskgroup_details["types"][key($task_type_array)] = array($task_type_array[key($task_type_array)], key($task_type_array));
//     	}

//         return $taskgroup_details;
//     }

// 	public function getTaskGroup($id) {
//         return $this->getObject('TaskGroup', $id);
//     }

//     public function flattenTaskGroupArray($statuses) {
//         $result_array = array();
//         if (!empty($statuses)) {
//             foreach($statuses as $status) {
//                 if (!is_bool($status[1])) {
//                     $result_array[$status[1]] = $status[0];
//                 } else {
//                     $result_array[] = $status[0];
//                 }
//             }
//         }

//         return $result_array;
//     }

//     public function getSelectArrayForTaskGroupType($class) {
//         $taskgroup_type_object = $this->getTaskGroupTypeObject($class);
//         if (!empty($taskgroup_type_object)) {
//             $status_array = $taskgroup_type_object->getStatusArray();
//             return $this->flattenTaskGroupArray($status_array);
//         }
//         return null;
//     }

//     // function to sort lists by date created
//     static function sortByCreated($a, $b) {
//         if ($a->dt_created == $b->dt_created) {
//             return 0;
//         }
//         return ($a->dt_created < $b->dt_created) ? +1 : -1;
//     }

// 	// public function getTasks($where = [], $sort = 'id', $sort_direction = 'DESC') {
// 	// 	$where = array_merge($where, ['is_deleted' => 0]);

// 	// 	return $this->getObjects('Task', $where, false, true, $sort ? $sort . ' ' . $sort_direction : null);
// 	// }

// 	public function getTask($id) {
// 		return $this->getObject('Task', $id);
// 	}

// 	/**
// 	 * Returns a list of assignable users
// 	 *
// 	 * @return Array
// 	 */
// 	public function getAssignableUsers() {

// 	}

// 	/**
// 	 * Summarises every task type that the user is a part of (via task group membership).
// 	 *
// 	 * @return Array
// 	 */
// 	public function getTaskTypeSummaryForUser() {
// 		// $member_of_task_groups = $this->_db->get("task_group_member")
//   //           ->leftJoin("task_group on task_group.id = task_group_member.task_group_id")->select()->select("DISTINCT task_group.id")
//   //           ->where("task_group_member.user_id", $user_id)->and("task_group_member.is_active", 1)
//   //           ->and("task_group.is_active", 1)->and("task_group.is_deleted", 0)->fetchAll();

//         $taskgroup_tasktypes = $this->w->db->get("task")->select()->select("DISTINCT task_type")
// 				->where("task.is_deleted", 0)->order_by("task_type ASC")->fetchAll();

// 		$task_types = [];

// 		if (!empty($taskgroup_tasktypes)) {
// 			foreach($taskgroup_tasktypes as $_task_type) {

// 			}
// 		}
// 	}

// 	// public function getTaskGroupDetailsForUser() {
//  //        $user_id = $this->w->Auth->user()->id;

// 	// 	// Replacing functionality in favour of speed
// 	// 	$member_of_task_groups = $this->_db->get("task_group_member")
//  //                ->leftJoin("task_group on task_group.id = task_group_member.task_group_id")->select()->select("DISTINCT task_group.id")
//  //                ->where("task_group_member.user_id", $user_id)->and("task_group_member.is_active", 1)
//  //                ->and("task_group.is_active", 1)->and("task_group.is_deleted", 0)->fetchAll();

// 	// 	$member_ids = [];
// 	// 	if (!empty($member_of_task_groups)) {
// 	// 		foreach ($member_of_task_groups as $member_of_task_group) {
// 	// 			$member_ids[]  = $member_of_task_group["id"];
// 	// 		}
// 	// 	}


// 	// 	$taskgroup_statuses = $this->w->db->get("task")->select()->select("DISTINCT status")
// 	// 			->where("task.is_deleted", 0)->order_by("status ASC")->fetchAll();
// 	// 	$statuses = [];

// 	// 	if (!empty($taskgroup_statuses)) {
// 	// 		foreach($taskgroup_statuses as $taskgroup_status) {
// 	// 			$statuses[] = $taskgroup_status['status'];
// 	// 		}
// 	// 	}

// 	// 	$taskgroup_priorities = $this->w->db->get("task")->select()->select("DISTINCT priority")
// 	// 			->where("task.is_deleted", 0)->order_by("priority ASC")->fetchAll();
// 	// 	$priorities = [];

// 	// 	if (!empty($taskgroup_priorities)) {
// 	// 		foreach($taskgroup_priorities as $taskgroup_priority) {
// 	// 			$priorities[] = $taskgroup_priority['priority'];
// 	// 		}
// 	// 	}

// 	// 	$taskgroup_tasktypes = $this->w->db->get("task")->select()->select("DISTINCT task_type")
// 	// 			->where("task.is_deleted", 0)->order_by("task_type ASC")->fetchAll();
// 	// 	$tasktypes = [];

// 	// 	if (!empty($taskgroup_tasktypes)) {
// 	// 		foreach($taskgroup_tasktypes as $taskgroup_tasktype) {
// 	// 			$tasktypes[] = $taskgroup_tasktype['task_type'];
// 	// 		}
// 	// 	}

// 	// 	$members = $this->w->db->get("task_group_member")->select()->select("DISTINCT task_group_member.user_id")->fetchAll();

// 	// 	$flat_members = [];
// 	// 	if (!empty($members)) {
// 	// 		foreach($members as $member) {
// 	// 			$flat_members[] = $member['user_id'];
// 	// 		}
// 	// 	}

// 	// 	$taskgroup_members = [];
// 	// 	if (!empty($flat_members)) {
// 	// 		$taskgroup_members = $this->getObjects("User", ["id" => $flat_members]);

// 	// 		uasort($taskgroup_members, function($a, $b) {
// 	// 			return strcmp($a->getFullName(), $b->getFullName());
// 	// 		});
// 	// 	}

// 	// 	return ["statuses" => $statuses, "priorities" => $priorities, "members" => $taskgroup_members, "types" => $tasktypes];
//  //    }

//     /**
//      * Task type functions
//      */

//     // return notify record for Task Group given taskgroup ID, role and type
//     function getTaskGroupNotifyType($id, $role, $type) {
//         return $this->getObject("TaskGroupNotify", array("task_group_id" => $id, "role" => $role, "type" => $type));
//     }

//     function sendCreationNotificationForTask($task) {
//         $subject = $task->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_CREATION) . "[" . $task->id . "]: " . $task->title;
// 	    $users_to_notify = $this->w->Task->getNotifyUsersForTask($task, TASK_NOTIFICATION_TASK_CREATION);

// 	    $this->w->Notification->sendToAllWithCallback($subject, "task", "notification_email", $this->w->Auth->user(), $users_to_notify, function($user, $existing_template_data) use ($task) {
// 	    	$template_data = $existing_template_data;
// 			$template_data['status']		= "[{$task->id}] New task created";
// 			$template_data['footer']		= $task->description;
// 			$template_data['action_url']	= $this->w->localUrl('/task/edit/' . $task->id);
// 			$template_data['logo_url']		= Config::get('main.application_logo');

// 			$this->w->Log->debug("Logo: " . $template_data['logo_url']);

// 			$template_data['fields'] = [
// 				"Assigned to"	=> !empty($task->assignee_id) ? $task->getAssignee()->getFullName() : '',
// 				"Type"			=> $task->getTypeTitle(),
// 				"Title"			=> $task->title,
// 				"Due"			=> !empty($task->dt_due) ? date('d-m-Y', strtotime(str_replace('/', '-', $task->dt_due))) : '',
// 				"Status"		=> $task->status,
// 				"Priority"		=> $task->isUrgent() ? "<b style='color: orange;'>{$task->priority}</b>" : $task->priority
// 			];

// 			if ($user->is_external) {
// 				$template_data['fields']['Due'] = '';
// 				$template_data['fields']['Priority'] = '';
// 				$template_data['fields']['Status'] = '';
// 			}

// 			$template_data['can_view_task'] = $user->is_external == 0;

// 			// Get additional details
// 			if ($user->is_external == 0) {
// 				$additional_details = $this->w->Task->getNotificationAdditionalDetails($task);
// 				if (!empty($additional_details)) {
// 					$template_data['footer'] .= $additional_details;
// 				}
// 			}

// 			if (!empty($task->assignee_id)) {
// 				if ($user->id == $task->assignee_id) {
// 					$template_data['fields']["Assigned to"] = "You (" . $task->getAssignee()->getFullName() . ")";
// 				} else {
// 					$template_data['fields']["Assigned to"] = !empty($task->assignee_id) ? $task->getAssignee()->getFullName() : '';
// 				}
// 			} else {
// 				$template_data['fields']["Assigned to"] = "No one";
// 			}

// 			return new NotificationCallback($user, $template_data, $this->w->file->getAttachmentsFileList($task, null, ['channel_email_raw']));
// 	    });
//     }

//     // static list of group permissions for can_view, can_assign, can_create
//     function getTaskGroupPermissions() {
//         return array("ALL", "GUEST", "MEMBER", "OWNER");
//     }

//     function getTaskGroupRoles() {
//         return array("GUEST", "MEMBER", "OWNER");
//     }

//     // determine if current user can perform a task
//     // compare users role against required role to perform given task
//     // function getMyPerms($role, $required_permission) {
//     //     $permissions = $this->getTaskGroupPermissions();

//     //     // key = permission level, value = ascending number
//     //     $i = 0;
//     //     $permission_array = array();
//     //     foreach ($permissions as $permission) {
//     //         $permission_array[$permission] = $i++;
//     //     }

//     //     // if number of user role is >= number of requesite level, then allow
//     //     if (!empty($permission_array[$role]) && !empty($permission_array[$required_permission])){
//     //         if ($permission_array[$role] >= $permission_array[$required_permission]) {
//     //             return true;
//     public function getTaskStatus($taskgroup) {
//         if (is_string($taskgroup) && class_exists($taskgroup)) {
//             $c = new $taskgroup($this->w);
//             if (is_a($c, "TaskGroupType")) {
//                 return $c->getStatusArray();
//             }
//         }
//     }

//     public function getTasks($where = array()) {
//         $where["is_deleted"] = 0;

//         return $this->getObjects("Task", $where);
//     }

//     // return a task group from the database given its ID
//     function getTasksbyGroupId($id) {
//         $where = ($id) ? array("task_group_id" => $id) : null;
//         return $this->getObjects("Task", $where);
//     }

//     // given a where clause, return all tasks created by a given user ID
//     // required to join with modifiable aspect to determine task creator
//     // function getCreatorTasks($id, $clause = null) {
//     //     if (is_array($clause)) {
//     //         foreach ($clause as $name => $value) {
//     //             $where .= "and t." . $name . " = '" . $value . "' ";
    
//     public function getTaskTypeStatus($taskgroup) {
//     	$statuses = [];
//         $task_status = $this->getTaskStatus($taskgroup);
//         if ($task_status) {
//             foreach ($task_status as $status) {
//                 $statuses[] = array($status[0], $status[0]);
//             }
//             return $statuses;
//         }
//     }

//     public function _loadTaskFiles() {
//         // do this only once
//         if ($this->_tasks_loaded) {
//             return;
//         }

//         $models = $this->w->modules();
//         foreach ($models as $model) {
//             $file = $this->w->getModuleDir($model) . $model . ".tasks.php";
//             if (file_exists($file)) {
//                 require_once $file;
//             }
//         }
//         $this->_tasks_loaded = true;
//     }


// 	public function navigation(Web $w, $title = null, $nav = null) {
//         if ($title) {
//             $w->ctx("title", $title);
//         }

//         $nav = $nav ? $nav : array();

//         if ($w->Auth->loggedIn()) {
//             $w->menuLink("task/index", "Task Dashboard", $nav);
//             $w->menuLink("task/list", "Task List", $nav);
//             $w->menuLink("task-group/list", "Task Group List", $nav);
//         }
//         $w->ctx("navigation", $nav);
//         return $nav;
//     }

//     // return an array for display of task type for a task group defined in our tasks file.
//     function getTaskTypes($taskgroup) {
//         if (empty($taskgroup)) {
//             return null;
//         }

//         $tasktypes = array();
//         $this->_loadTaskFiles();
//         foreach (get_declared_classes() as $class) {
//             if ($class == $taskgroup) {
//                 $tgt = new $class($this->w);
//                 foreach ($tgt->getTaskTypeArray() as $short_tasktype => $long_tasktype) {
//                     $tasktypes[] = array($long_tasktype, $short_tasktype);
//                 }
//             }
//         }
//         return $tasktypes;
//     }

//     // returns an array for display of priorities of a task group defined in our tasks file
//     function getTaskPriority($taskgroup) {
//         $taskprior = array();
//         $this->_loadTaskFiles();
//         if (class_exists($taskgroup)) {
//             $tgt = new $taskgroup($this->w);
//             if (is_a($tgt, "TaskGroupType")) {
//                 $priority = $tgt->getTaskPriorityArray();
//                 foreach ($priority as $taskpriority) {
//                     $taskprior[] = array($taskpriority, $taskpriority);
//                 }
//             }
//         }
//         return $taskprior;
//     }

//     // returns the additional form fields for a task type as defined in our task file
//     function getFormFieldsByTask($tasktype, TaskGroup $tg) {
//         $fieldform = array();
//         $this->_loadTaskFiles();
//         $fieldform = null;
//         foreach (get_declared_classes() as $class) {
//             if (startsWith($class, "TaskType_" . $tasktype)) {
//                 $tgt = new $class($this->w);
//                 $fieldform = $tgt->getFieldFormArray($tg);
//             }
//         }
//         return $fieldform;
//     }

//     // return a task comment by the COMMENT ID
//     function getComment($id) {
//         return $this->w->Auth->getObject("TaskComment", array("obj_table" => Task::$_db_table, "id" => $id));
//     }

// //    function getTaskTimes() {
// //        return $this->getObjects("TaskTime", array("is_deleted" => 0, "user_id" => $this->w->Auth->user()->id));
// //    }

//     // return a time log entry by log entry ID
// //    function getTimeLogEntry($id) {
// //        return $this->getObject("TaskTime", array("id" => $id, "is_deleted" => 0));
// //    }

//     // return an array of the owners of a task group from the database
//     function getTaskGroupOwners($id) {
//         return $this->getObjects("TaskGroupMember", array("task_group_id" => $id, "role" => "OWNER", "is_active" => 1));
//     }

//     // determine if a given user is an owner of a task group.
//     // input: task group ID & user ID
//     function getIsOwner($task_group_id, $user_id) {
//         $owners = $this->getTaskGroupOwners($task_group_id);
//         if ($owners) {
//             foreach ($owners as $owner) {
//                 if ($owner->user_id == $user_id)
//                     return true;
//             }
//         }
//         return false;
//     }

//     // return all groups from the database of which a user is a member, given user ID. else, return all groups
//     function getMemberGroups($id = null) {
//         if (empty($id)) {
//             return null;
//         }

//         $query = $this->_db->get("task_group_member")
//                 ->leftJoin("task_group")
//                 ->where("task_group_member.user_id", $id)->and("task_group_member.is_active", 1)
//                 ->and("task_group.is_active", 1)->and("task_group.is_deleted", 0);
//         return $this->getObjectsFromRows("TaskGroupMember", $query->fetch_all());
//     }

//     function getTaskGroupsForMember($id = null) {
//         if (empty($id)) {
//             return null;
//         }

//         $query = $this->_db->get("task_group_member")
//                 ->leftJoin("task_group")->select("task_group.*")
//                 ->where("task_group_member.user_id", $id)->and("task_group_member.is_active", 1)
//                 ->and("task_group.is_active", 1)->and("task_group.is_deleted", 0);
//         return $this->getObjectsFromRows("TaskGroup", $query->fetch_all());
//     }

//     // return all members of a task group from the database, given the task group ID
//     function getMemberGroup($id) {
//         return $this->getObjects("TaskGroupMember", array("task_group_id" => $id, "is_active" => 1));
//     }

//     // return an array for display of all members in a given task group, by task group ID
//     function getMembersInGroup($id) {
// 		$line = [];
//         $members = $this->getObjects("TaskGroupMember", array("task_group_id" => $id, "is_active" => 1));
// 		if (!empty($members)) {
// 			foreach ($members as $member) {
// 				$line[] = array($this->getUserById($member->user_id), $member->user_id);
// 			}
// 		}
//         return $line;
//     }

//     // return an array for display of all members of a task group who can be assigned tasks, given task group ID
//     function getMembersBeAssigned($id) {
//         $line = array();
//         $where = "task_group_id = " . $id . " and (role = 'MEMBER' or role = 'OWNER') and is_active = 1";
//         $members = $this->getObjects("TaskGroupMember", $where);
//         if (!empty($members)) {
//             foreach ($members as $member) {
//                 $line[] = array($this->getUserById($member->user_id), $member->user_id);
//             }
//         }
//         return $line;
//     }

//     // return a users full name given their user ID
//     function getUserById($id) {
//         $u = $this->w->Auth->getUser($id);
//         return $u ? $u->getFullName() : "";
//     }

//     // prepare to get all task types of type $class as defined in our tasks file
//     function getTaskTypeObject($class) {
//         return $this->_getTaskObjectGeneric($class, "TaskType_");
//     }

//     // get all task groups or task types of type $class as defined in our task file
//     function _getTaskObjectGeneric($class, $type) {
//         $this->_loadTaskFiles();
//         $class = startsWith($class, $type) ? $class : $type . $class;
//         if (class_exists($class)) {
//             return new $class($this->w);
//         }
//         return null;
//     }

//     // nicely format a number of seconds as H:m
//     function getFormatPeriod($seconds) {
//         if (is_numeric($seconds)) {
//             $hours = intval($seconds / 3600);
//             $mins = intval(($seconds / 60) % 60);
//             $mins = str_pad($mins, 2, "0", STR_PAD_LEFT);
//             return $hours . ":" . $mins;
//         }
//     }

//     // function to sort task group list by task type
//     static function sortbyGroup($a, $b) {
//         if (strcasecmp($a->task_group_type, $b->task_group_type) == 0) {
//             return 0;
//         }
//         return (strcasecmp($a->task_group_type, $b->task_group_type) > 0) ? +1 : -1;
//     }

//     // prepare to get all task groups of type $class as defined in our tasks file
//     function getTaskGroupTypeObject($class) {
//         return $this->_getTaskObjectGeneric($class, "TaskGroupType_");
//     }

//     /**
//      * Create a new Task
//      *
//      * @param unknown $task_type
//      * @param unknown $task_group_id
//      * @param unknown $title
//      * @param unknown $description
//      * @param unknown $priority
//      * @param unknown $dt_due
//      * @param unknown $first_assignee_id
//      */
//     function createTask($task_type, $task_group_id, $title, $description, $priority, $dt_due, $first_assignee_id, $_skip_creation_notification = false) {
//         $task = new Task($this->w);
//         $task->task_type = $task_type;
//         $task->task_group_id = $task_group_id;
//         $task->title = $title;
//         $task->description = $description;
//         $task->priority = $priority;
//         $task->dt_due = $dt_due;
//         $task->first_assignee_id = $first_assignee_id;
//         $task->assignee_id = $first_assignee_id;
//         $task->_skip_creation_notification = $_skip_creation_notification;
//         $task->insert();
//         return $task;
//     }

//     // static list of group permissions for can_view, can_assign, can_create
//     // function getTaskGroupPermissions() {
//     //     return array("ALL", "GUEST", "MEMBER", "OWNER");
//     // }

//     /**
//      * Create a new Taskgroup using all the form details of the taskgroup form
//      *
//      * @param task_group_type, eg. "TaskGroupType_TaskTodo"
//      * @param title, the task group title
//      * @param can_assign, OWNER|MEMBER|GUEST
//      * @param can_view, OWNER|MEMBER|GUEST
//      * @param can_create, OWNER|MEMBER|GUEST
//      * @param is_active, 0|1
//      * @param is_deleted, 0|1
//      * @param description, a description
//      * @param default_assignee_id, a user_id or null
//      *
//      * @return TaskGroup
//      */
//     // function createTaskGroup($type, $title, $description, $default_assignee_id, $can_assign = "OWNER", $can_view = "OWNER", $can_create = "OWNER", $is_active = 1, $is_deleted = 0, $default_task_type = null, $default_priority = null, $is_automatic_subscription = false) {
//     //     // title should be unique!
//     //     $taskgroup = $this->getTaskGroupByUniqueTitle($title);
//     //     if (null != $taskgroup) {
//     //         return $taskgroup;
//     //     }

//     //     // insert newly created task group into the task_group database
//     //     $taskgroup = new TaskGroup($this->w);
//     //     $taskgroup->task_group_type = $type;
//     //     $taskgroup->title = $title;
//     //     $taskgroup->description = $description;
//     //     $taskgroup->can_assign = $can_assign;
//     //     $taskgroup->can_view = $can_view;
//     //     $taskgroup->can_create = $can_create;
//     //     $taskgroup->is_active = $is_active;
//     //     $taskgroup->is_deleted = !empty($is_deleted) ? $is_deleted : 0;
//     //     $taskgroup->default_assignee_id = $default_assignee_id;
//     //     $taskgroup->default_task_type = $default_task_type;
//     //     $taskgroup->default_priority = $default_priority;
// 	// 	$taskgroup->is_automatic_subscription = !!$is_automatic_subscription;
//     //     $response = $taskgroup->insert();

//     //     // Check the validation
//     //     if ($response !== true) {
//     //         $this->w->errorMessage($taskgroup, "Taskgroup", $response, false, "/task-group/viewtaskgrouptypes#create");
//     //     }

//     //     // if created succcessfully, create default notify matrix: all on
//     //     if ($taskgroup->id) {
//     //         $arr['guest']['creator'] = 1;
//     //         $arr['member']['creator'] = 1;
//     //         $arr['member']['assignee'] = 1;
//     //         $arr['owner']['creator'] = 1;
//     //         $arr['owner']['assignee'] = 1;
//     //         $arr['owner']['other'] = 1;

//     //         // so foreach role/type lets put the values in the database
//     //         foreach ($arr as $role => $types) {
//     //             foreach ($types as $type => $value) {
//     //                 $notify = new TaskGroupNotify($this->w);
//     //                 $notify->task_group_id = $taskgroup->id;
//     //                 $notify->role = $role;
//     //                 $notify->type = $type;
//     //                 $notify->value = $value;
//     //                 $notify->insert();
//     //             }

//     // get all task group types as defined in our tasks file
//     function getAllTaskGroupTypes() {
//         $this->_loadTaskFiles();
//         foreach (get_declared_classes() as $class) {
//             if (startsWith($class, "TaskGroupType_")) {
//                 $tgt = new $class($this->w);
//                 $taskgrouptypes[] = array($tgt->getTaskGroupTypeTitle(), $class);
//             }
//         }
//         return $taskgrouptypes;
//     }

//     public function getNotifyUsersForTask($task, $event) {
//         if (empty($task)) {
//             return array();
//         }

//         $me = array();
//         // This may be called from cron
//         if (!empty($_SESSION['user_id'])) {
//             $me = array($this->getMemberGroupById($task->task_group_id, $_SESSION['user_id']));
//         }

//         // get member object for task creator
//         $creator_id = $task->getTaskCreatorId();

// 		// Notify assignee too
//         $creator = array($this->getMemberGroupById($task->task_group_id, $creator_id), !empty($task->assignee_id) ? $this->getMemberGroupById($task->task_group_id, $task->assignee_id) : null);
//         // get member object(s) for task group owner(s)
//         $owners = $this->getTaskGroupOwners($task->task_group_id);

//         // us is everyone
// 		if (empty($owners) || !is_array($owners)) {
// 			$owners = [];
// 		}
//         $us = (object) array_merge($me, $creator, $owners);

//         if (empty($us)) {
//             return array();
//         }

//         $notifyUsers = array();

//         // foreach relavent member
//         foreach ($us as $i) {
//             if (empty($i)) {
//                 continue;
//             }

//             // set default notification value. 0 = no notification
//             $shouldNotify = false; // $value = "0";
//             // set current user's role
//             $role = strtolower($i->role);
//             // determine current user's 'type' for this task
//             $assignee = ($task->assignee_id == $i->user_id);
//             $creator = ($creator_id == $i->user_id);
//             $owner = $this->getIsOwner($task->task_group_id, $i->user_id);

//             // this user may be any or all of the 'types'
//             // need to check each 'type' for a notification
//             $types = array();
//             if (!empty($assignee)) {
//                 $types[] = "assignee";
//             }
//             if (!empty($creator)) {
//                 $types[] = "creator";
//             }
//             if (!empty($owner)) {
//                 $types[] = "other";
//             }

//             // if they have a type ... look for notifications
//             if (!empty($types)) {
//                 // check user task notifications
//                 $notify = $this->getTaskUserNotify($i->user_id, $task->id);
//                 // if there is a record, get notification flag
//                 if (!empty($notify)) {
//                     $shouldNotify = (bool) $notify->$event; // $value = $notify->$event;
//                 }
//                 // if no user task notification present, check user task group notification for role and type
//                 else {

//                     // for each type, check the User defined notification table
//                     foreach ($types as $type) {
//                         $notify = $this->getTaskGroupUserNotifyType($i->user_id, $task->task_group_id, $role, $type);
//                         // if there is a notification flag and it equals 1, no need to go further, a notification will be sent
//                         if (!empty($notify)) {
//                             if ($notify->value == "1") {
//                                 $shouldNotify = (bool) $notify->$event;
//                                 break;
//                             }
//                         }
//                     }
//                 }

//                 // if no user task group notification present, check task group default notification for role and type
//                 if (empty($notify)) {
//                     foreach ($types as $type) {
//                         $notify = $this->getTaskGroupNotifyType($task->task_group_id, $role, $type);
//                         // if notification exists, set its value
//                         if (!empty($notify)) {
//                             $shouldNotify = (bool) $notify->value;
//                         }
//                         // if its value is 1, no need to go further, a notification will be sent
//                         if ($shouldNotify) {
//                             break;
//                         }
//                     }
//                 }
//                 // if somewhere we have found a positive notification, add user_id to our send list
//                 if ($shouldNotify) {
//                     $notifyUsers[$i->user_id] = $i->user_id;
//                 }
//             }
//             unset($types);
//         }
//         return $notifyUsers;
//     }

// 	public function getNotificationAdditionalDetails(Task $task) {
// 		$additional_details = $this->w->callHook("task", "notification_additional_details", $task);
// 		$message = '';

// 		if (!empty($additional_details)) {
// //			$message .= "<br/><p>Additional details:</p>";
// 			foreach($additional_details as $additional_detail) {
// 				if (!empty($additional_detail)) {
// 					$message .= "<p>" . $additional_detail . "</p>";
// 				}
// 			}
// 		}

// 		return !empty($message) ? "<br/><p>Additional details:</p>" . $message : '';
// 	}

}
