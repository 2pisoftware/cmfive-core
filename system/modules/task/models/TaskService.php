<?php

class TaskService extends DbService {

	private $_tasks_loaded = false;

	public function __construct($w) {
		parent::__construct($w);

		$this->_loadTaskFiles();
	}

    public function getTaskGroups() {
        return $this->getObjects('TaskGroup', ['is_active' => 1, 'is_deleted' => 0]);
    }

	public function getTaskGroup($id) {
        return $this->getObject('TaskGroup', $id);
    }

	public function getTasks($where = [], $sort = 'id', $sort_direction = 'DESC') {
		$where = array_merge($where, ['is_deleted' => 0]);

		return $this->getObjects('Task', $where, false, true, $sort ? $sort . ' ' . $sort_direction : null);
	}

	public function getTask($id) {
		return $this->getObject('Task', $id);
	}

	/**
	 * Returns a list of assignable users
	 *
	 * @return Array
	 */
	public function getAssignableUsers() {

	}

	/**
	 * Summarises every task type that the user is a part of (via task group membership).
	 *
	 * @return Array
	 */
	public function getTaskTypeSummaryForUser() {
		// $member_of_task_groups = $this->_db->get("task_group_member")
  //           ->leftJoin("task_group on task_group.id = task_group_member.task_group_id")->select()->select("DISTINCT task_group.id")
  //           ->where("task_group_member.user_id", $user_id)->and("task_group_member.is_active", 1)
  //           ->and("task_group.is_active", 1)->and("task_group.is_deleted", 0)->fetchAll();

        $taskgroup_tasktypes = $this->w->db->get("task")->select()->select("DISTINCT task_type")
				->where("task.is_deleted", 0)->order_by("task_type ASC")->fetchAll();

		$task_types = [];

		if (!empty($taskgroup_tasktypes)) {
			foreach($taskgroup_tasktypes as $_task_type) {

			}
		}
	}

	// public function getTaskGroupDetailsForUser() {
 //        $user_id = $this->w->Auth->user()->id;

	// 	// Replacing functionality in favour of speed
	// 	$member_of_task_groups = $this->_db->get("task_group_member")
 //                ->leftJoin("task_group on task_group.id = task_group_member.task_group_id")->select()->select("DISTINCT task_group.id")
 //                ->where("task_group_member.user_id", $user_id)->and("task_group_member.is_active", 1)
 //                ->and("task_group.is_active", 1)->and("task_group.is_deleted", 0)->fetchAll();

	// 	$member_ids = [];
	// 	if (!empty($member_of_task_groups)) {
	// 		foreach ($member_of_task_groups as $member_of_task_group) {
	// 			$member_ids[]  = $member_of_task_group["id"];
	// 		}
	// 	}


	// 	$taskgroup_statuses = $this->w->db->get("task")->select()->select("DISTINCT status")
	// 			->where("task.is_deleted", 0)->order_by("status ASC")->fetchAll();
	// 	$statuses = [];

	// 	if (!empty($taskgroup_statuses)) {
	// 		foreach($taskgroup_statuses as $taskgroup_status) {
	// 			$statuses[] = $taskgroup_status['status'];
	// 		}
	// 	}

	// 	$taskgroup_priorities = $this->w->db->get("task")->select()->select("DISTINCT priority")
	// 			->where("task.is_deleted", 0)->order_by("priority ASC")->fetchAll();
	// 	$priorities = [];

	// 	if (!empty($taskgroup_priorities)) {
	// 		foreach($taskgroup_priorities as $taskgroup_priority) {
	// 			$priorities[] = $taskgroup_priority['priority'];
	// 		}
	// 	}

	// 	$taskgroup_tasktypes = $this->w->db->get("task")->select()->select("DISTINCT task_type")
	// 			->where("task.is_deleted", 0)->order_by("task_type ASC")->fetchAll();
	// 	$tasktypes = [];

	// 	if (!empty($taskgroup_tasktypes)) {
	// 		foreach($taskgroup_tasktypes as $taskgroup_tasktype) {
	// 			$tasktypes[] = $taskgroup_tasktype['task_type'];
	// 		}
	// 	}

	// 	$members = $this->w->db->get("task_group_member")->select()->select("DISTINCT task_group_member.user_id")->fetchAll();

	// 	$flat_members = [];
	// 	if (!empty($members)) {
	// 		foreach($members as $member) {
	// 			$flat_members[] = $member['user_id'];
	// 		}
	// 	}

	// 	$taskgroup_members = [];
	// 	if (!empty($flat_members)) {
	// 		$taskgroup_members = $this->getObjects("User", ["id" => $flat_members]);

	// 		uasort($taskgroup_members, function($a, $b) {
	// 			return strcmp($a->getFullName(), $b->getFullName());
	// 		});
	// 	}

	// 	return ["statuses" => $statuses, "priorities" => $priorities, "members" => $taskgroup_members, "types" => $tasktypes];
 //    }

    /**
     * Task type functions
     */

    public function getTaskStatus($taskgroup) {
        if (is_string($taskgroup) && class_exists($taskgroup)) {
            $c = new $taskgroup($this->w);
            if (is_a($c, "TaskGroupType")) {
                return $c->getStatusArray();
            }
        }
    }

    public function getTaskTypeStatus($taskgroup) {
    	$statuses = [];
        $task_status = $this->getTaskStatus($taskgroup);
        if ($task_status) {
            foreach ($task_status as $status) {
                $statuses[] = array($status[0], $status[0]);
            }
            return $statuses;
        }
    }

    public function _loadTaskFiles() {
        // do this only once
        if ($this->_tasks_loaded) {
            return;
        }

        $models = $this->w->modules();
        foreach ($models as $model) {
            $file = $this->w->getModuleDir($model) . $model . ".tasks.php";
            if (file_exists($file)) {
                require_once $file;
            }
        }
        $this->_tasks_loaded = true;
    }


	public function navigation(Web $w, $title = null, $nav = null) {
        if ($title) {
            $w->ctx("title", $title);
        }

        $nav = $nav ? $nav : array();

        if ($w->Auth->loggedIn()) {
            $w->menuLink("task/index", "Task Dashboard", $nav);
            $w->menuLink("task/list", "Task List", $nav);
            $w->menuLink("task-group/viewtaskgrouptypes", "Task Groups", $nav);
        }
        $w->ctx("navigation", $nav);
        return $nav;
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

    // return a users full name given their user ID
    function getUserById($id) {
        $u = $this->w->Auth->getUser($id);
        return $u ? $u->getFullName() : "";
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

    // nicely format a number of seconds as H:m
    function getFormatPeriod($seconds) {
        if (is_numeric($seconds)) {
            $hours = intval($seconds / 3600);
            $mins = intval(($seconds / 60) % 60);
            $mins = str_pad($mins, 2, "0", STR_PAD_LEFT);
            return $hours . ":" . $mins;
        }
    }

    // function to sort task group list by task type
    static function sortbyGroup($a, $b) {
        if (strcasecmp($a->task_group_type, $b->task_group_type) == 0) {
            return 0;
        }
        return (strcasecmp($a->task_group_type, $b->task_group_type) > 0) ? +1 : -1;
    }

    // prepare to get all task groups of type $class as defined in our tasks file
    function getTaskGroupTypeObject($class) {
        return $this->_getTaskObjectGeneric($class, "TaskGroupType_");
    }

    // static list of group permissions for can_view, can_assign, can_create
    function getTaskGroupPermissions() {
        return array("ALL", "GUEST", "MEMBER", "OWNER");
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
}
