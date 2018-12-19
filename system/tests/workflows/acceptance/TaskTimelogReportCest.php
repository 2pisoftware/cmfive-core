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
		$I->login($I, 'admin', 'admin');
        $I->createTaskGroup($I, 'Test Taskgroup',[
    		'task_group_type'=>'To Do',
    		'can_assign'=>'MEMBER',
    		'can_view'=>'GUEST',
    		'can_create'=>'MEMBER',
    		'is_active'=>'Yes',
    		'description'=>'A test group',
    	]);
        $I->addMemberToTaskGroup($I, 'Test Taskgroup', 'Administrator', 'OWNER');
        $I->createTask($I, 'Test Taskgroup', 'Test Task', [
    		'task_type'=>'To Do',
    		'title'=>'Test Task',
    		'status'=>'New',
    		'priority'=>'Normal',
    		'dt_due'=>strtotime('27/05/1988'),
    		'assignee_id'=>'Administrator',
    		'estimate_hours'=>10,
    		'effort'=>11,
    		'description'=>'a test task',
    	]);
        $I->createTimelogFromTimer($I, 'Test Task', '1:00 am');
        $I->createTimelog($I, '1 - Test Task', '1/1/2000', '1:00 am', '3:00 pm');

        $I->createReport($I, 'Test Report', 'Task');
        $I->click('SQL');
        $report_string = "[[task_id||autocomplete||Task Number (Required)||&nbsp;&nbsp;&nbsp;select t.id as value, concat(CAST(t.id AS CHAR),' ',t.title) as title&nbsp;&nbsp;&nbsp;&nbsp;from task t&nbsp;&nbsp;&nbsp;&nbsp;where t.is_deleted = 0&nbsp;&nbsp;&nbsp;order by title ]] @@Task Details|| SELECT t.id as 'Number', concat('{{webroot}}task/edit/',t.id) as Number_link, t.title as 'Title', t.description as 'Description', t.task_type as 'Type', t.status as 'Status', t.priority as 'Priority', DATE_FORMAT(t.dt_due, '%Y/%m/%d') as 'Due Date', t.estimate_hours as 'Estimated Hours', t.effort as 'Effort', CONCAT(assignee.firstname, ' ', assignee.lastname) as 'Assigned to'&nbsp;&nbsp;FROM task t LEFT JOIN user AS assignee_user ON t.assignee_id = assignee_user.id LEFT JOIN contact AS assignee ON assignee_user.contact_id = assignee.id&nbsp;&nbsp;WHERE t.id = '{{task_id}}' @@ @@Task Comments|| SELECT CONCAT(creator.firstname,' ',creator.lastname) as 'User', DATE_FORMAT(c.dt_created, '%Y/%m/%d %H:%i') as 'Date Created', c.comment as 'Comment'&nbsp;&nbsp;FROM comment c LEFT JOIN user AS creator_user ON c.creator_id = creator_user.id LEFT JOIN contact AS creator ON creator_user.contact_id = creator.id&nbsp;&nbsp;WHERE c.obj_table = 'task' AND c.is_system = 0 AND c.is_deleted = 0 AND c.obj_id = '{{task_id}}'&nbsp;&nbsp;ORDER BY c.dt_created @@ @@Task Timelog Entries|| SELECT&nbsp;&nbsp;CONCAT(c.firstname,' ',c.lastname) as 'User', tl.time_type as 'Time Type', tl.dt_start as 'Date Time Started', tl.dt_end as 'Date Time Finished', sec_to_time(unix_timestamp(tl.dt_end) - unix_timestamp(tl.dt_start)) as 'Duration'&nbsp;&nbsp;FROM timelog tl LEFT JOIN user u ON tl.creator_id = u.id LEFT JOIN contact c ON u.contact_id = c.id&nbsp;&nbsp;WHERE tl.is_deleted = 0 AND tl.object_class = 'Task' AND tl.object_id = '{{task_id}}'&nbsp;&nbsp;ORDER BY tl.dt_start @@ @@Total Time On Task|| SELECT sec_to_time(SUM(unix_timestamp(tl.dt_end) - unix_timestamp(tl.dt_start))) as 'Total' FROM timelog tl WHERE tl.is_deleted = 0 AND tl.object_class = 'Task' AND tl.object_id = '{{task_id}}' @@ @@Task Attachments|| SELECT a.title as 'Title', concat('{{webroot}}file/atfile/',a.id) as Title_link, a.description as 'Description'&nbsp;&nbsp;FROM attachment a&nbsp;&nbsp;WHERE a.is_deleted = 0 AND a.parent_table = 'task' AND a.parent_id = '{{task_id}}' @@";
        $I->executeJS("$('.CodeMirror')[0].CodeMirror.setValue(" . $report_string . ")");
	}

}
