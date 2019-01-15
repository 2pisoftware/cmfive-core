<?php

// This test replicates the cm5_test_suite_one html selenium test
// Creates a Task and Timelogs for said task
// Then creates a report that uses the task and timelogs

class TaskTimelogReportCest
{
    var $lookupTitle = 'Your Highness';
    var $username = 'TestUser';
    var $firstname = 'Firstname';
    var $lastname = 'Lastname';

	public function testTaskTimelogReport($I) {
		$I->loginAsAdmin($I);
		$I->createUser($I, $this->username, 'password', $this->firstname, $this->lastname, 'Firstname@lastname.com', ['comment', 'favorites_user', 'file_upload', 'help_view', 'help_contact', 'inbox_reader', 'inbox_sender', 'user', 'report_user', 'tag_user', 'task_admin', 'task_user', 'task_group', 'timelog_user']);
        $I->createTaskGroup($I, 'Test Taskgroup',[
    		'task_group_type'=>'To Do',
    		'can_assign'=>'MEMBER',
    		'can_view'=>'GUEST',
    		'can_create'=>'MEMBER',
    		'is_active'=>'Yes',
    		'description'=>'A test group',
    	]);
        $I->addMemberToTaskGroup($I, 'Test Taskgroup', $this->firstname.' '.$this->lastname, 'OWNER');
        $I->createTask($I, 'Test Taskgroup', 'Test Task', [
    		'task_type'=>'To Do',
    		'title'=>'Test Task',
    		'status'=>'New',
    		'priority'=>'Normal',
    		'dt_due'=>strtotime('27/05/1988'),
    		'assignee_id'=>$this->firstname.' '.$this->lastname,
    		'estimate_hours'=>10,
    		'effort'=>11,
    		'description'=>'a test task',
    	]);
        $I->createTimelogFromTimer($I, 'Test Task', '1:00 am');
        $I->createTimelog($I, '1 - Test Task', '1/1/2000', '1:00 am', '3:00 pm');

		$I->createReport($I, 'Test Report', 'Task');
		$report_string = 
		"				[[task_id||autocomplete||Task Number (Required)|| ".
		"				select t.id as value, concat(CAST(t.id AS CHAR),' ',t.title) as title  from task t".
		"				where t.is_deleted = 0   order by title ]] ".
		"				@@Task Details".
		"				|| SELECT t.id as 'Number', concat('{{webroot}}task/edit/',t.id) as Number_link".
		"				, t.title as 'Title', t.description as 'Description', t.task_type as 'Type'".
		"				, t.status as 'Status', t.priority as 'Priority'".
		"				, DATE_FORMAT(t.dt_due, '%Y/%m/%d') as 'Due Date'".
		"				, t.estimate_hours as 'Estimated Hours', t.effort as 'Effort'".
		"				, CONCAT(assignee.firstname, ' ', assignee.lastname) as 'Assigned to'  ".
		"				FROM task t LEFT JOIN user AS assignee_user ".
		"				ON t.assignee_id = assignee_user.id ".
		"				LEFT JOIN contact AS assignee ".
		"				ON assignee_user.contact_id = assignee.id  ".
		"				WHERE t.id = '{{task_id}}' @@ ".
		"				@@Task Comments".
		"				|| SELECT CONCAT(creator.firstname,' ',creator.lastname) as 'User'".
		"					, DATE_FORMAT(c.dt_created, '%Y/%m/%d %H:%i') as 'Date Created'".
		"					, c.comment as 'Comment'  FROM comment c ".
		"					LEFT JOIN user AS creator_user ON c.creator_id = creator_user.id ".
		"					LEFT JOIN contact AS creator ON creator_user.contact_id = creator.id  ".
		"					WHERE c.obj_table = 'task' AND c.is_system = 0 ".
		"					AND c.is_deleted = 0 AND c.obj_id = '{{task_id}}'  ".
		"					ORDER BY c.dt_created @@ ".
		"				@@Task Timelog Entries".
		"				|| SELECT  CONCAT(c.firstname,' ',c.lastname) as 'User'".
		"					, tl.time_type as 'Time Type'".
		"					, tl.dt_start as 'Date Time Started', tl.dt_end as 'Date Time Finished'".
		"					, sec_to_time(unix_timestamp(tl.dt_end) - unix_timestamp(tl.dt_start)) as 'Duration'".
		"						FROM timelog tl LEFT JOIN user u ".
		"						ON tl.creator_id = u.id ".
		"						LEFT JOIN contact c ".
		"						ON u.contact_id = c.id  ".
		"						WHERE tl.is_deleted = 0 AND tl.object_class = 'Task' ".
		"						AND tl.object_id = '{{task_id}}'  ORDER BY tl.dt_start @@ @@Total Time On Task".
		"				|| SELECT sec_to_time(SUM(unix_timestamp(tl.dt_end) - unix_timestamp(tl.dt_start))) as 'Total' ".
		"				FROM timelog tl WHERE tl.is_deleted = 0 AND tl.object_class = 'Task' ".
		"					AND tl.object_id = '{{task_id}}' @@ ".
		"				@@Task Attachments".
		"				|| SELECT a.title as 'Title', concat('{{webroot}}file/atfile/',a.id) as Title_link".
		"					, a.description as 'Description'  ".
		"					FROM attachment a  WHERE a.is_deleted = 0 AND a.parent_table = 'task' ".
		"					AND a.parent_id = '{{task_id}}' @@		";
		 $I->defineReportSQL($I, 'Test Report', $report_string);  
		 $I->requestReport($I, 'Test Report');
		 $I->executeJS("$('#acp_task_id').autocomplete('search', 'Test Task')");
		 $I->click('1 Test Task');
		 $I->click('Display Report');
		 $I->wait(5);
		 $I->see('Task Details');
		 $I->see('Todo');
		 $I->see('Firstname Lastname');
		 $I->see('Duration');
		 $I->see('14:00:00');
	}

}
