<?php

function task_list_GET(Web $w) {
        /*$filter = !empty($_GET) ? $_GET : [];
        $q = null;
        if (!empty($filter)) {
            $limit = (int)$filter["limit"];
            $orderBy = $filter["orderBy"];
            $page = (int)$filter["page"];
            $offset = ($page * $limit) - $limit;
            $filterBy = array_key_exists("query", $filter) ? $filter["query"] : "";
            $search = $filter["params"];
            $searchQuery = "";
            
            if (!empty($filterBy)) {
                $searchQuery = " where";
                foreach ($filterBy as $key => $value) {
                    $searchQuery .= " $key like '%$value%'";
                }
            }
            
            if (!empty($search)) {
                $searchQuery = " where";
                foreach ($search as $key => $value) {
                    $searchQuery .= " $key like '%$value%' and";
                }
                
                $searchQuery = preg_replace('/and$/', '', $searchQuery);
            }
            
            $q = $w->db->query("select * from (select t.id, t.task_group_id, t.title, tg.title as task_group_title, t.assignee_id, concat(c.firstname, ' ', c.lastname) as assignee_name, t.task_type, t.priority, t.status, t.dt_due from task t inner join task_group tg on t.task_group_id = tg.id inner join user u on t.assignee_id = u.id inner join contact c on u.contact_id = c.id) t $searchQuery $orderBy limit $offset, $limit")->fetchAll();
            
            for ($a = 0; $a < count($q); $a++) {
                foreach($q[$a] as $column => $value) {
                    if (is_numeric($column)) {
                        unset($q[$a][$column]);
                    }
                }
            }
        }*/
    
	$tasks = $w->Task->getTasks();

	$tasks_as_array = array_map(function($task) use ($w) {
		$task_array = $task->toArray();
		$task_group = $task->getTaskGroup();

		$task_array['task_url'] = $w->localUrl($task->printSearchUrl());
		$task_array['task_group_title'] = $task_group->title; // ->toLink();
		$task_array['task_group_url'] = $w->localUrl($task_group->printSearchUrl());
		$task_array['assignee_name'] = $task->getAssignee()->getSelectOptionTitle();
		$task_array['dt_due'] = formatDate($task->dt_due);

		return $task_array;
	}, $tasks ? : []);
        
        /*$data = [
            'data' => $tasks_as_array,
            'count' => count($tasks_as_array)
	];*/

        $w->out((new JsonResponse())->setSuccessfulResponse('OK', $tasks_as_array));
}

function task_group_list_GET(Web $w) {
	
	$task_groups = $w->Task->getTaskGroups();

	$task_groups_as_array = array_map(function($task_group) {
		$task_group_array = $task_group->toArray();

		return $task_group_array;
	}, $task_groups ? : []);

	$w->out((new JsonResponse())->setSuccessfulResponse('OK', $task_groups_as_array));

}

function user_details_GET(Web $w) {

	list($user_id) = $w->pathMatch('user_id');

	$user = $w->Auth->getUser($user_id);
	if (empty($user->id) || !$user->canView($w->Auth->user())) {
		$w->out((new JsonResponse())->setMissingResponse('User not found'));
		return;
	}

	$data = [
		'name' => $user->getFullName(),
		'email' => $user->getContact()->email,
		'gravatar_hash' => md5(trim($user->getContact()->email))
	];

	$w->out((new JsonResponse())->setSuccessfulResponse('OK', $data));
}

function assignee_autocomplete_GET(Web $w) {
    $filter = $_GET['filter'];
    $assignees = array_map(function($user) {return ['value' => $user['fullname'], 'text' => $user['fullname']];}, $w->db->query("select distinct t.assignee_id, concat(c.firstname, ' ', c.lastname) as fullname from task t inner join `user` u on u.id = t.assignee_id inner join contact c on u.contact_id = c.id where c.firstname like '$filter%' or c.lastname like '$filter%';")->fetchAll());
    $w->out((new JsonResponse())->setSuccessfulResponse('OK', $assignees));
}