<?php
class tasksCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }

    // auth details
	var $username='admin';
	var $password='admin';
	
	public function testTasks($I) {
		$I->login($I,$this->username,$this->password);
		$I->createUser($I,'testuser','password','testy','tererer','testy@tererer.com');
		$I->createTaskGroup($I,'testgroup',[
			'task_group_type'=>'To Do',
			'can_assign'=>'GUEST',
			'can_view'=>'GUEST',
			'can_create'=>'GUEST',
			'is_active'=>'Yes',
			'description'=>'A test group',
		//	'default_assignee_id'=>'testy tererer'
		]);
		$I->updateTaskGroup($I,'testgroup',[
			'title'=>'testgroup updated',
			'can_assign'=>'MEMBER',
			'can_view'=>'MEMBER',
			'can_create'=>'MEMBER',
			'is_active'=>'Yes',
			'description'=>'A test group updated',
		//	'default_assignee_id'=>'testy tererer'
		]);
		$I->addMemberToTaskGroup($I,'testgroup updated','testy tererer','MEMBER');
		
		$I->createTask($I,'testgroup','test task',[
			'task_group_id'=>'testgroup',
			'task_type'=>'To Do',
			'title'=>'test task',
			'status'=>'New',
			'priority'=>'Normal',
			'dt_due'=>strtotime('27/05/1988'),
			'assignee_id'=>'testy tererer',
			'estimate_hours'=>10,
			'effort'=>11,
			'description'=>'a test task',
		]);
		$I->updateMemberInTaskGroup($I,'testgroup updated','testy tererer','ALL');
		$I->removeMemberFromTaskGroup($I,'testgroup updated','testy tererer');
		$I->deleteTaskGroup($I,'testgroup updated');
	}
}
