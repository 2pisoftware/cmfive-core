<?php
class TaskModuleCest
{

    	 
    public function _before()
    {
    }

    public function _after()
    {
    }

	public function testTaskModule(\Step\Acceptance\CmfiveTaskModule $I) {
         
    $I->wantTo('Verify that the tasks module is functioning correctly');
		$I->login('admin','admin');
    $I->createUser('testTasks_testuser' ,'password','testTasks_test','user','test@user.com');
    $I->clickCmfiveNavbar('Task', 'New Task');
    // since there are no task groups, we are redirected and asked to create a task group
    $I->canSeeInCurrentUrl('/task-group/viewtaskgrouptypes#dashboard');
    $I->see('Please set up a taskgroup before continuing');
		$I->createTaskGroup('testTasks_testgroup',[
			'task_group_type'=>'To Do',
			'can_assign'=>'GUEST',
			'can_view'=>'GUEST',
			'can_create'=>'GUEST',
			'is_active'=>'Yes',
			'description'=>'A test group',
		]);
    $I->addMemberToTaskGroup('testTasks_testgroup','testTasks_test user','MEMBER');
		$I->updateTaskGroup('testTasks_testgroup',[
			'title'=>'testTasks_testgroup updated',
			'can_assign'=>'MEMBER',
			'can_view'=>'MEMBER',
			'can_create'=>'MEMBER',
			'is_active'=>'Yes',
			'description'=>'A test group updated',
			'default_assignee_id'=>'testTasks_test user'
		]);

		$I->createTask('testTasks_testgroup updated','testTasks_test task',[
			'task_type'=>'To Do',
			'title'=>'testTasks_test task',
			'status'=>'New',
			'priority'=>'Normal',
			'dt_due'=>strtotime('27/05/1988'),
			'assignee_id'=>'testTasks_test user',
			'estimate_hours'=>10,
			'effort'=>11,
			'description'=>'a test task',
		]);
    $I->click('Duplicate Task');
    $I->see('Task duplicated');
    $I->see('testTasks_test task -Copy');
    $I->editTask('testTasks_test task -Copy', ['status' => 'Wip']);
    $I->see('Wip');
		$I->updateMemberInTaskGroup('testTasks_testgroup updated','testTasks_test user','ALL');
		$I->removeMemberFromTaskGroup('testTasks_testgroup updated','testTasks_test user');
		$I->deleteTaskGroup('testTasks_testgroup updated');
	}

}
