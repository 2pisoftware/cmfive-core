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

	public function getTasks() {
		return $this->getObjects('Task', ['is_deleted' => 0]);
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
        }
        $w->ctx("navigation", $nav);
        return $nav;
    }
}