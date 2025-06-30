<?php

class TaskService extends DbService
{

    public $_tasks_loaded;

    public function getSubscriber($subscriber_id)
    {
        return $this->getObject("TaskSubscriber", $subscriber_id);
    }

    public function getSubscriberForUserAndTask($user_id, $task_id)
    {
        return $this->getObject("TaskSubscriber", ["is_deleted" => 0, "user_id" => $user_id, "task_id" => $task_id]);
    }

    public function getTaskGroupDetailsForUser()
    {
        $user_id = AuthService::getInstance($this->w)->user()->id;

        // Replacing functionality in favour of speed
        $member_of_task_groups = $this->_db->get("task_group_member")
            ->leftJoin("task_group on task_group.id = task_group_member.task_group_id")->select()->select("DISTINCT task_group.id")
            ->where("task_group_member.user_id", $user_id)->and("task_group_member.is_active", 1)
            ->and("task_group.is_active", 1)->and("task_group.is_deleted", 0)->fetchAll();

        $member_ids = [];
        if (!empty($member_of_task_groups)) {
            foreach ($member_of_task_groups as $member_of_task_group) {
                $member_ids[] = $member_of_task_group["id"];
            }
        }

        $taskgroup_statuses = $this->w->db->get("task")->select()->select("DISTINCT status")
            ->where("task.is_deleted", 0)->orderBy("status ASC")->fetchAll();
        $statuses = [];

        if (!empty($taskgroup_statuses)) {
            foreach ($taskgroup_statuses as $taskgroup_status) {
                $statuses[] = $taskgroup_status['status'];
            }
        }

        $taskgroup_priorities = $this->w->db->get("task")->select()->select("DISTINCT priority")
            ->where("task.is_deleted", 0)->orderBy("priority ASC")->fetchAll();
        $priorities = [];

        if (!empty($taskgroup_priorities)) {
            foreach ($taskgroup_priorities as $taskgroup_priority) {
                $priorities[] = $taskgroup_priority['priority'];
            }
        }

        $taskgroup_tasktypes = $this->w->db->get("task")->select()->select("DISTINCT task_type")
            ->where("task.is_deleted", 0)->orderBy("task_type ASC")->fetchAll();
        $tasktypes = [];

        if (!empty($taskgroup_tasktypes)) {
            foreach ($taskgroup_tasktypes as $taskgroup_tasktype) {
                $tasktypes[] = $taskgroup_tasktype['task_type'];
            }
        }

        $members = $this->w->db->get("task_group_member")->select()->select("DISTINCT task_group_member.user_id")->fetchAll();

        $flat_members = [];
        if (!empty($members)) {
            foreach ($members as $member) {
                $flat_members[] = $member['user_id'];
            }
        }

        $taskgroup_members = [];
        if (!empty($flat_members)) {
            $taskgroup_members = $this->getObjects("User", ["id" => $flat_members]);

            uasort($taskgroup_members, function ($a, $b) {
                return strcmp($a->getFullName(), $b->getFullName());
            });
        }

        return ["statuses" => $statuses, "priorities" => $priorities, "members" => $taskgroup_members, "types" => $tasktypes];
    }

    public function getTaskGroupDetailsForTaskGroup($taskgroup_id)
    {
        $taskgroup = TaskService::getInstance($this->w)->getTaskGroup($taskgroup_id);

        $taskgroup_details = ["taskgroups" => [], "statuses" => [], "priorities" => [], "members" => [], "types" => []];
        if (!empty($taskgroup)) {
            $taskgroup_details["taskgroups"][] = $taskgroup;
            $taskgroup_details["statuses"] = $taskgroup->getStatus();
            $taskgroup_details["priorities"] = $taskgroup->getPriority();
            $taskgroup_details["members"] = $this->getMembersInGroup($taskgroup->id);
            $task_type_array = $taskgroup->getTaskGroupTypeObject()->getTaskTypeArray();
            $taskgroup_details["types"][key($task_type_array)] = [$task_type_array[key($task_type_array)], key($task_type_array)];
        }

        return $taskgroup_details;
    }

    public function flattenTaskGroupArray($statuses)
    {
        $result_array = [];
        if (!empty($statuses)) {
            foreach ($statuses as $status) {
                if (!is_bool($status[1])) {
                    $result_array[$status[1]] = $status[0];
                } else {
                    $result_array[] = $status[0];
                }
            }
        }

        return $result_array;
    }

    public function getSelectArrayForTaskGroupType($class)
    {
        $taskgroup_type_object = $this->getTaskGroupTypeObject($class);
        if (!empty($taskgroup_type_object)) {
            $status_array = $taskgroup_type_object->getStatusArray();
            return $this->flattenTaskGroupArray($status_array);
        }
        return null;
    }

    // function to sort lists by date created
    public static function sortByCreated($a, $b)
    {
        if ($a->dt_created == $b->dt_created) {
            return 0;
        }
        return ($a->dt_created < $b->dt_created) ? +1 : -1;
    }

    // function to sort task time log by date started
    public static function sortByStarted($a, $b)
    {
        if ($a->dt_start == $b->dt_start) {
            return 0;
        }
        return ($a->dt_start > $b->dt_start) ? +1 : -1;
    }

    // function to sort task group list by task type
    public static function sortbyGroup($a, $b)
    {
        if (strcasecmp($a->task_group_type, $b->task_group_type) == 0) {
            return 0;
        }
        return (strcasecmp($a->task_group_type, $b->task_group_type) > 0) ? +1 : -1;
    }

    // function to sort task lists by due date
    public static function sortTasksbyDue($a, $b)
    {
        if ($a->dt_due == $b->dt_due) {
            return 0;
        }
        return ($a->dt_due > $b->dt_due) ? +1 : -1;
    }

    // function to sort groups lists by users role
    public static function sortbyRole($a, $b)
    {
        if ($a->role == $b->role) {
            return 0;
        }
        return ($a->role > $b->role) ? +1 : -1;
    }

    // convert dd/mm/yyyy date to yyy-mm-dd for SQL statements
    public function date2db($date)
    {
        if ($date && strpos($date, "/") > -1) {
            list($d, $m, $y) = preg_split("/\/|-|\./", $date);
            return $y . "-" . $m . "-" . $d;
        }
        return $date;
    }

    // nicely format a number of seconds as H:m
    public function getFormatPeriod($seconds)
    {
        if (is_numeric($seconds)) {
            $hours = intval($seconds / 3600);
            $mins = intval(intval($seconds / 60) % 60);
            $mins = str_pad($mins, 2, "0", STR_PAD_LEFT);
            return $hours . ":" . $mins;
        }
    }

    // return a date one week behind 'today'
    public function getLastWeek()
    {
        $cd = strtotime(date("Y-m-d"));
        $newdate = date("d/m/Y", mktime(0, 0, 0, date("m", $cd), date("d", $cd) - 7, date("Y", $cd)));
        return $newdate;
    }

    // return a date one month in advance of 'today'
    public function getNextMonth()
    {
        $cd = strtotime(date("Y-m-d"));
        $newdate = date("d/m/Y", mktime(0, 0, 0, date("m", $cd) + 1, date("d", $cd), date("Y", $cd)));
        return $newdate;
    }

    // mark up URLS as <a> links
    public function findURL($text)
    {
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
    public function getTaskGroup($id)
    {
        return $this->getObject("TaskGroup", $id);
    }

    // get all active task groups from the database
    public function getTaskGroups($include_inactive = false)
    {
        $where = ["is_deleted" => 0];
        if (!$include_inactive) {
            $where['is_active'] = 1;
        }
        return $this->getObjects("TaskGroup", $where);
    }

    // get all task groups from the database of given task group type
    public function getTaskGroupsByType($id)
    {
        return $this->getObjects("TaskGroup", ["is_active" => 1, "is_deleted" => 0, "task_group_type" => $id]);
    }

    // get all task group types as defined in our tasks file
    public function getAllTaskGroupTypes()
    {
        $this->_loadTaskFiles();
        foreach (get_declared_classes() as $class) {
            if (startsWith($class, "TaskGroupType_")) {
                $tgt = new $class($this->w);
                $taskgrouptypes[] = [$tgt->getTaskGroupTypeTitle(), $class];
            }
        }
        return $taskgrouptypes;
    }

    // prepare to get all task groups of type $class as defined in our tasks file
    public function getTaskGroupTypeObject($class)
    {
        return $this->_getTaskObjectGeneric($class, "TaskGroupType_");
    }

    // prepare to get all task types of type $class as defined in our tasks file
    public function getTaskTypeObject($class)
    {
        return $this->_getTaskObjectGeneric($class, "TaskType_");
    }

    // get all task groups or task types of type $class as defined in our task file
    public function _getTaskObjectGeneric($class, $type)
    {
        $this->_loadTaskFiles();
        $class = startsWith($class, $type) ? $class : $type . $class;
        if (class_exists($class)) {
            return new $class($this->w);
        }
        return null;
    }

    // return the task group type by a task group ID
    public function getTaskGroupTypeById($id)
    {
        $c = $this->getTaskGroup($id);
        if (!empty($c->id)) {
            return $c->task_group_type;
        }
        return null;
    }

    // return the task group type by a task group ID
    public function getTaskGroupTitleById($id)
    {
        $c = $this->getTaskGroup($id);
        return $c->title;
    }

    // return the task group description as defined in our tasks file for a given type/class
    public function getTaskGroupDescription($class)
    {
        $this->_loadTaskFiles();
        $tgt = new $class($this->w);
        return $tgt->getTaskGroupTypeDescription();
    }

    // return the task group flag, re: can tasks be reopened as defined in our tasks file for a given type/class
    public function getCanTaskReopen($taskgroup)
    {
        $this->_loadTaskFiles();
        if (class_exists($taskgroup)) {
            $c = new $taskgroup($this->w);
            return $c->getCanTaskGroupReopen();
        }
        return false;
    }

    // return user notify record given task ID, user id
    public function getTaskUserNotify($id, $tid)
    {
        return $this->getObject("TaskUserNotify", ["user_id" => $id, "task_id" => $tid]);
    }

    // return all notify records given user id and taskgroup ID
    public function getTaskGroupUserNotify($id, $tid)
    {
        return $this->getObjects("TaskGroupUserNotify", ["user_id" => $id, "task_group_id" => $tid]);
    }

    // return notify record for user given user id, taskgroup ID, role and type
    public function getTaskGroupUserNotifyType($id, $tid, $role, $type)
    {
        return $this->getObject("TaskGroupUserNotify", ["user_id" => $id, "task_group_id" => $tid, "role" => $role, "type" => $type]);
    }

    // return the recordset of notify matrix for given Task Group
    public function getTaskGroupNotify($id)
    {
        return $this->getObjects("TaskGroupNotify", ["task_group_id" => $id]);
    }

    // return notify record for Task Group given taskgroup ID, role and type
    public function getTaskGroupNotifyType($id, $role, $type)
    {
        return $this->getObject("TaskGroupNotify", ["task_group_id" => $id, "role" => $role, "type" => $type]);
    }

    public function sendCreationNotificationForTask($task)
    {
        $subject = $task->getHumanReadableAttributeName(TASK_NOTIFICATION_TASK_CREATION) . "[" . $task->id . "]: " . $task->title;
        $users_to_notify = TaskService::getInstance($this->w)->getNotifyUsersForTask($task, TASK_NOTIFICATION_TASK_CREATION);

        NotificationService::getInstance($this->w)->sendToAllWithCallback($subject, "task", "notification_email", AuthService::getInstance($this->w)->user(), $users_to_notify, function ($user, $existing_template_data) use ($task) {
            $template_data = $existing_template_data;
            $template_data['status'] = "[{$task->id}] New task created";
            $template_data['footer'] = $task->description;
            $template_data['action_url'] = $this->w->localUrl('/task/edit/' . $task->id);
            $template_data['logo_url'] = Config::get('main.application_logo');

            $template_data['fields'] = [
                "Assigned to" => !empty($task->assignee_id) ? $task->getAssignee()->getFullName() : '',
                "Type" => $task->getTypeTitle(),
                "Title" => $task->title,
                "Due" => !empty($task->dt_due) ? date('d-m-Y', strtotime(str_replace('/', '-', $task->dt_due))) : '',
                "Status" => $task->status,
                "Priority" => $task->isUrgent() ? "<b style='color: orange;'>{$task->priority}</b>" : $task->priority,
            ];

            if ($user->is_external) {
                $template_data['fields']['Due'] = '';
                $template_data['fields']['Priority'] = '';
                $template_data['fields']['Status'] = '';
            }

            $template_data['can_view_task'] = $user->is_external == 0;

            // Get additional details
            if ($user->is_external == 0) {
                $additional_details = TaskService::getInstance($this->w)->getNotificationAdditionalDetails($task);
                if (!empty($additional_details)) {
                    $template_data['footer'] .= $additional_details;
                }
            }

            if (!empty($task->assignee_id)) {
                if ($user->id == $task->assignee_id) {
                    $template_data['fields']["Assigned to"] = "You (" . $task->getAssignee()->getFullName() . ")";
                } else {
                    $template_data['fields']["Assigned to"] = !empty($task->assignee_id) ? $task->getAssignee()->getFullName() : '';
                }
            } else {
                $template_data['fields']["Assigned to"] = "No one";
            }

            return new NotificationCallback($user, $template_data, FileService::getInstance($this->w)->getAttachmentsFileList($task, null, ['channel_email_raw']));
        });
    }

    public function sendSubscribeNotificationForTask($task, $user)
    {
        $subject = "Added as subscriber to: [" . $task->id . "] " . $task->title;
        $users_to_notify = [$user->id => $user->id];

        NotificationService::getInstance($this->w)->sendToAllWithCallback($subject, "task", "notification_email", AuthService::getInstance($this->w)->user(), $users_to_notify, function ($user, $existing_template_data) use ($task) {
            $template_data = $existing_template_data;
            $template_data['status'] = "You've been added as a subscriber to: [{$task->id}]{$task->title}";
            $template_data['footer'] = $task->description;
            $template_data['action_url'] = $this->w->localUrl('/task/edit/' . $task->id);
            $template_data['logo_url'] = Config::get('main.application_logo');

            $template_data['fields'] = [
                "Assigned to" => !empty($task->assignee_id) ? $task->getAssignee()->getFullName() : '',
                "Type" => $task->getTypeTitle(),
                "Title" => $task->title,
                "Due" => !empty($task->dt_due) ? date('d-m-Y', strtotime(str_replace('/', '-', $task->dt_due))) : '',
                "Status" => $task->status,
                "Priority" => $task->isUrgent() ? "<b style='color: orange;'>{$task->priority}</b>" : $task->priority,
            ];

            if ($user->is_external) {
                $template_data['fields']['Due'] = '';
                $template_data['fields']['Priority'] = '';
                $template_data['fields']['Status'] = '';
            }

            $template_data['can_view_task'] = $user->is_external == 0;

            // Get additional details
            if ($user->is_external == 0) {
                $additional_details = TaskService::getInstance($this->w)->getNotificationAdditionalDetails($task);
                if (!empty($additional_details)) {
                    $template_data['footer'] .= $additional_details;
                }
            }

            if (!empty($task->assignee_id)) {
                if ($user->id == $task->assignee_id) {
                    $template_data['fields']["Assigned to"] = "You (" . $task->getAssignee()->getFullName() . ")";
                } else {
                    $template_data['fields']["Assigned to"] = !empty($task->assignee_id) ? $task->getAssignee()->getFullName() : '';
                }
            } else {
                $template_data['fields']["Assigned to"] = "No one";
            }

            return new NotificationCallback($user, $template_data, FileService::getInstance($this->w)->getAttachmentsFileList($task, null, ['channel_email_raw']));
        });
    }

    // static list of group permissions for can_view, can_assign, can_create
    public function getTaskGroupPermissions()
    {
        return ["ALL", "GUEST", "MEMBER", "OWNER"];
    }

    public function getTaskGroupRoles()
    {
        return ["GUEST", "MEMBER", "OWNER"];
    }

    // determine if current user can perform a task
    // compare users role against required role to perform given task
    public function getMyPerms($role, $required_permission)
    {
        $permissions = $this->getTaskGroupPermissions();

        // key = permission level, value = ascending number
        $i = 0;
        $permission_array = [];
        foreach ($permissions as $permission) {
            $permission_array[$permission] = $i++;
        }

        // if number of user role is >= number of requesite level, then allow
        if (!empty($permission_array[$role]) && array_key_exists($required_permission, $permission_array)) {
            if ($permission_array[$role] >= $permission_array[$required_permission]) {
                return true;
            }
        }
        return false;
    }

    public function getTasks($where = [])
    {
        $where["is_deleted"] = 0;

        return $this->getObjects("Task", $where);
    }

    // return a task group from the database given its ID
    public function getTasksbyGroupId($id)
    {
        $where = ($id) ? ["task_group_id" => $id] : null;
        return $this->getObjects("Task", $where);
    }

    // given a where clause, return all tasks created by a given user ID
    // required to join with modifiable aspect to determine task creator
    public function getCreatorTasks($id, $clause = null)
    {
        $where = '';
        if (is_array($clause)) {
            foreach ($clause as $name => $value) {
                $where .= "and t." . $name . " = '" . $value . "' ";
            }
        } elseif ($clause != "") {
            $where = " and " . $clause;
        }
        $where .= " and t.is_deleted = 0 and g.is_active = 1 and g.is_deleted = 0";

        // check that task group is active and not deleted
        $rows = $this->_db->sql("SELECT t.* from " . Task::$_db_table . " as t inner join " . ObjectModification::$_db_table . " as o on t.id = o.object_id inner join " . TaskGroup::$_db_table . " as g on t.task_group_id = g.id where o.creator_id = " . $this->_db->quote($id) . " and o.table_name = '" . Task::$_db_table . "' " . $this->_db->quote($where) . " order by t.id")->fetchAll();
        $rows = $this->fillObjects("Task", $rows);
        return $rows;
    }

    // return all resulting tasks from the database modified in the last week
    public function getTaskWeek($group, $assignee, $from, $to)
    {
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

        if ($assignee != "") {
            $who = " c.creator_id = " . $assignee . " and ";
        }

        // create where clause giving all active tasks which have shown activity in the last week
        // need to check if task group is deleted
        $grps = "t.task_group_id in (" . $grplist . ") and ";
        $where = "where " . $grps . $who . " t.is_deleted = 0 and g.is_active = 1 and g.is_deleted = 0";
        $where .= " and date_format(c.dt_modified,'%Y-%m-%d') >= '" . $this->date2db($from) . "' and date_format(c.dt_modified,'%Y-%m-%d') <= '" . $this->date2db($to) . "'";

        // get and return tasks
        $rowQry = "SELECT t.id, t.title, t.task_group_id, c.comment, c.creator_id, c.dt_modified from " . Task::$_db_table . " as t inner join "
         . TaskComment::$_db_table . " as c on t.id = c.obj_id and c.obj_table = '" . Task::$_db_table . "' inner join "
          . TaskGroup::$_db_table . " as g on t.task_group_id = g.id " . $where . " order by c.dt_modified desc";
        $rows = $this->_db->sql($rowQry)->fetchAll();
        return $rows;
    }

    // get a task from the database given its ID
    public function getTask($id)
    {
        return $this->getObject("Task", $id);
    }

    // get the task data from the database given a task ID
    public function getTaskData($id)
    {
        return $this->getObjects("TaskData", ["task_id" => $id]);
    }

    public function getTaskByTaskDataKeyValuePair($key, $value)
    {
        $taskdata = TaskService::getInstance($this->w)->getObject("TaskData", ["data_key" => $key, "value" => $value]);
        if (!empty($taskdata->id)) {
            return $this->getTask($taskdata->task_id);
        }
        return null;
    }

    // return an array for display of task type for a task group defined in our tasks file.
    public function getTaskTypes($taskgroup)
    {
        if (empty($taskgroup)) {
            return null;
        }

        $tasktypes = [];
        $this->_loadTaskFiles();
        foreach (get_declared_classes() as $class) {
            if ($class == $taskgroup) {
                $tgt = new $class($this->w);
                foreach ($tgt->getTaskTypeArray() as $short_tasktype => $long_tasktype) {
                    $tasktypes[] = [$long_tasktype, $short_tasktype];
                }
            }
        }
        return $tasktypes;
    }

    // returns an array of statuses of a task group defined in our tasks file
    public function getTaskStatus($taskgroup)
    {
        $this->_loadTaskFiles();
        if (is_string($taskgroup) && class_exists($taskgroup)) {
            $c = new $taskgroup($this->w);
            if (is_a($c, "TaskGroupType")) {
                return $c->getStatusArray();
            }
        }
    }

    // returns an array for display of statuses of a task group defined in our tasks file
    public function getTaskTypeStatus($taskgroup)
    {
        $this->_loadTaskFiles();
        $arrstatus = $this->getTaskStatus($taskgroup);
        if ($arrstatus) {
            foreach ($arrstatus as $status) {
                $statuses[] = [$status[0], $status[0]];
            }
            return $statuses;
        }
    }

    // returns an array for display of priorities of a task group defined in our tasks file
    public function getTaskPriority($taskgroup)
    {
        $taskprior = [];
        $this->_loadTaskFiles();
        if (class_exists($taskgroup)) {
            $tgt = new $taskgroup($this->w);
            if (is_a($tgt, "TaskGroupType")) {
                $priority = $tgt->getTaskPriorityArray();
                foreach ($priority as $taskpriority) {
                    $taskprior[] = [$taskpriority, $taskpriority];
                }
            }
        }
        return $taskprior;
    }

    // returns the additional form fields for a task type as defined in our task file
    public function getFormFieldsByTask($tasktype, TaskGroup $tg)
    {
        $fieldform = [];
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
    public function getComment($id)
    {
        return AuthService::getInstance($this->w)->getObject("TaskComment", ["obj_table" => Task::$_db_table, "id" => $id]);
    }

    // return an array of the owners of a task group from the database
    public function getTaskGroupOwners($id)
    {
        return $this->getObjects("TaskGroupMember", ["task_group_id" => $id, "role" => "OWNER", "is_active" => 1]);
    }

    // determine if a given user is an owner of a task group.
    // input: task group ID & user ID
    public function getIsOwner($task_group_id, $user_id)
    {
        $owners = $this->getTaskGroupOwners($task_group_id);
        if ($owners) {
            foreach ($owners as $owner) {
                if ($owner->user_id == $user_id) {
                    return true;
                }
            }
        }
        return false;
    }

    // return all groups from the database of which a user is a member, given user ID. else, return all groups
    public function getMemberGroups($id = null)
    {
        if (empty($id)) {
            return null;
        }

        $query = $this->_db->get("task_group_member")
            ->leftJoin("task_group")
            ->where("task_group_member.user_id", $id)->and("task_group_member.is_active", 1)
            ->and("task_group.is_active", 1)->and("task_group.is_deleted", 0);
        return $this->getObjectsFromRows("TaskGroupMember", $query->fetchAll());
    }

    public function getTaskGroupsForMember($id = null)
    {
        if (empty($id)) {
            return null;
        }

        $query = $this->_db->get("task_group_member")
            ->leftJoin("task_group")->select("task_group.*")
            ->where("task_group_member.user_id", $id)->and("task_group_member.is_active", 1)
            ->and("task_group.is_active", 1)->and("task_group.is_deleted", 0);
        return $this->getObjectsFromRows("TaskGroup", $query->fetchAll());
    }

    // return all members of a task group from the database, given the task group ID
    public function getMemberGroup($id)
    {
        return $this->getObjects("TaskGroupMember", ["task_group_id" => $id, "is_active" => 1]);
    }

    // return an array for display of all members in a given task group, by task group ID
    public function getMembersInGroup($id)
    {
        $line = [];
        $members = $this->getObjects("TaskGroupMember", ["task_group_id" => $id, "is_active" => 1]);
        if (!empty($members)) {
            foreach ($members as $member) {
                $line[] = [$this->getUserById($member->user_id), $member->user_id];
            }
        }
        return $line;
    }

    // return an array for display of all members of a task group who can be assigned tasks, given task group ID
    public function getMembersBeAssigned($id)
    {
        $line = [];
        $where = "task_group_id = " . $id . " and (role = 'MEMBER' or role = 'OWNER') and is_active = 1 and user_id > 0";
        $members = $this->getObjects("TaskGroupMember", $where);

        if (!empty($members)) {
            foreach ($members as $member) {
                $line[] = [$this->getUserById($member->user_id), $member->user_id];
            }
        }

        return $line;
    }

    // return a member object given the task_group_member database ID: targets specific member in specific task group
    public function getMemberById($id)
    {
        return $this->getObject("TaskGroupMember", ["id" => $id]);
    }

    // return a member object given a task group ID and a user ID
    public function getMemberGroupById($group, $uid)
    {
        return $this->getObject("TaskGroupMember", ["task_group_id" => $group, "user_id" => $uid, "is_active" => 1]);
    }

    // return a users full name given their user ID
    public function getUserById($id)
    {
        $u = AuthService::getInstance($this->w)->getUser($id);
        return $u ? StringSanitiser::sanitise($u->getFullName()) : "";
    }

    // load our task files to make available: titles, descriptions, status, additional form fields, etc.
    // for defined task groups amd task types
    public function _loadTaskFiles()
    {
        // do this only once
        if ($this->_tasks_loaded) {
            return;
        }

        $handlers = $this->w->modules();
        foreach ($handlers as $model) {
            $file = $this->w->getModuleDir($model) . $model . ".tasks.php";
            if (file_exists($file)) {
                require_once $file;
            }
        }
        $this->_tasks_loaded = true;
    }

    public function getTaskGroupByUniqueTitle($title)
    {
        return $this->getObject("TaskGroup", ["title" => $title, "is_deleted" => 0]);
    }

    public function addMemberToTaskGroup($taskgroup_id, $user_id, $role = "GUEST")
    {
        if (empty($taskgroup_id) || empty($user_id)) {
            return;
        }

        // Check that they're not already a member
        $member = $this->getObject("TaskGroupMember", ["task_group_id" => $taskgroup_id, "user_id" => $user_id]);
        if (!empty($member)) {
            return;
        }

        $taskgroupmember = new TaskGroupMember($this->w);
        $taskgroupmember->task_group_id = $taskgroup_id;
        $taskgroupmember->user_id = $user_id;
        $taskgroupmember->role = $role;
        $taskgroupmember->is_active = 1;
        $taskgroupmember->insert();
    }

    public function removeMemberFromTaskGroup($taskgroup_id, $user_id)
    {
        $tgm = $this->getObject("TaskGroupMember", ["task_group_id" => $taskgroup_id, "user_id" => $user_id]);
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
    public function createTask($task_type, $task_group_id, $title, $description, $priority, $dt_due, $first_assignee_id, $_skip_creation_notification = false)
    {
        $task = new Task($this->w);
        $task->task_type = $task_type;
        $task->task_group_id = $task_group_id;
        $task->title = $title;
        $task->description = $description;
        $task->priority = $priority;
        $task->dt_due = $dt_due;
        $task->first_assignee_id = $first_assignee_id;
        $task->assignee_id = $first_assignee_id;
        $task->_skip_creation_notification = $_skip_creation_notification;
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
    public function createTaskGroup($type, $title, $description, $default_assignee_id, $can_assign = "OWNER", $can_view = "OWNER", $can_create = "OWNER", $is_active = 1, $is_deleted = 0, $default_task_type = null, $default_priority = null, $is_automatic_subscription = true)
    {
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
            $arr['member']['other'] = 1;
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
            $arrdb = [];
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

    public function getNotifyUsersForTask($task, $event)
    {
        if (empty($task)) {
            return [];
        }

        /*
        $me = [];
        // This may be called from cron
        if (!empty($_SESSION['user_id'])) {
            $me = [$this->getMemberGroupById($task->task_group_id, $_SESSION['user_id'])];
        }

        // get member object for task creator
        $creator_id = $task->getTaskCreatorId();

        // Notify assignee too
        $creator = [$this->getMemberGroupById($task->task_group_id, $creator_id), !empty($task->assignee_id) ? $this->getMemberGroupById($task->task_group_id, $task->assignee_id) : null];
        // get member object(s) for task group owner(s)

        $users = $this->getTaskGroupUsers($task->task_group_id);

        // us is everyone
        if (empty($users) || !is_array($users)) {
            $users = [];
        }

        $us = (object) array_merge($me, $creator, $users);

        if (empty($us)) {
            return [];
        }

        // foreach relavent member
        foreach ($us as $i) {
            if (empty($i)) {
                continue;
            }

            // set default notification value. 0 = no notification
            $shouldNotify = false;
            // set current user's role
            $role = strtolower($i->role);
            // determine current user's 'type' for this task
            $assignee = ($task->assignee_id == $i->user_id);
            $creator = ($creator_id == $i->user_id);

            if ($this->getIsOwner($task->task_group_id, $i->user_id) || $this->getIsMember($task->task_group_id, $i->user_id))
            {
                $user = true;
            }

            // this user may be any or all of the 'types'
            // need to check each 'type' for a notification
            $types = [];
            if (!empty($assignee)) {
                $types[] = "assignee";
            }
            if (!empty($creator)) {
                $types[] = "creator";
            }
            if (!empty($user)) {
                $types[] = "other";
            }


            // if they have a type ... look for notifications
            if (!empty($types)) {
                // check user task notifications
                $notify = $this->getTaskUserNotify($i->user_id, $task->id);
                // if there is a record, get notification flag
                if (!empty($notify)) {
                    $shouldNotify = (bool) $notify->$event;
                } else {
                    // if no user task notification present, check user task group notification for role and type
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
        }*/

        $notifyUsers = [];

        $subs = $task->getSubscribers();
        foreach ($subs as $sub) {
            $user = $sub->getUser();
            if ($user->is_external == 1) {
                continue;
            }

            $notifyUsers[$sub->user_id] = $sub->user_id;
        }

        return $notifyUsers;
    }

    public function getNotificationAdditionalDetails(Task $task)
    {
        $additional_details = $this->w->callHook("task", "notification_additional_details", $task);
        $message = '';

        if (!empty($additional_details)) {
            foreach ($additional_details as $additional_detail) {
                if (!empty($additional_detail)) {
                    $message .= "<p>" . $additional_detail . "</p>";
                }
            }
        }

        return !empty($message) ? "<br/><p>Additional details:</p>" . $message : '';
    }

    public function navigation(Web $w, $title = null, $nav = null)
    {
        if ($title) {
            $w->ctx("title", $title);
        }

        $nav = $nav ? $nav : [];

        if (AuthService::getInstance($w)->loggedIn()) {
            $w->menuLink("task/index", "Task Dashboard", $nav);
            $w->menuLink("task/edit", "New Task", $nav);
            $w->menuLink("task/tasklist", "Task List", $nav);
            $w->menuLink("task/notifications", "Notifications", $nav);
            $w->menuLink("task/taskweek", "Activity", $nav);
            $w->menuLink("task-group/viewtaskgrouptypes", "Task Groups", $nav);
        }
        $w->ctx("navigation", $nav);
        return $nav;
    }

    public function navList(): array
    {
        return [
            new MenuLinkStruct("Task Dashboard", "task/index"),
            new MenuLinkStruct("New Task", "task/edit"),
            new MenuLinkStruct("Task List", "task/tasklist"),
            new MenuLinkStruct("Notifications", "task/notifications"),
            new MenuLinkStruct("Activity", "task/taskweek"),
            new MenuLinkStruct("Task Groups", "task-group/viewtaskgrouptypes"),
        ];
    }
}
