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
    $I->wantTo('Verify that the tasks module is functioning correctly');
		$I->login($this->username,$this->password);
		$I->createUser('testuser','password','test','user','test@user.com');
		$I->createTaskGroup('testgroup',[
			'task_group_type'=>'To Do',
			'can_assign'=>'GUEST',
			'can_view'=>'GUEST',
			'can_create'=>'GUEST',
			'is_active'=>'Yes',
			'description'=>'A test group',
		]);
    $I->addMemberToTaskGroup('testgroup','test user','MEMBER');
		$I->updateTaskGroup('testgroup',[
			'title'=>'testgroup updated',
			'can_assign'=>'MEMBER',
			'can_view'=>'MEMBER',
			'can_create'=>'MEMBER',
			'is_active'=>'Yes',
			'description'=>'A test group updated',
			'default_assignee_id'=>'test user'
		]);

		$I->createTask('testgroup updated','test task',[
			'task_type'=>'To Do',
			'title'=>'test task',
			'status'=>'New',
			'priority'=>'Normal',
			'dt_due'=>strtotime('27/05/1988'),
			'assignee_id'=>'test user',
			'estimate_hours'=>10,
			'effort'=>11,
			'description'=>'a test task',
		]);
		$I->updateMemberInTaskGroup('testgroup updated','test user','ALL');
		$I->removeMemberFromTaskGroup('testgroup updated','test user');
		$I->deleteTaskGroup('testgroup updated');
	}
}
