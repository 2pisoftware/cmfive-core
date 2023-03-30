<?php

class TaskModuleCest
{
    /**
     * Runs the Task module tests.
     *
     * @param CmfiveUI $I
     * @return void
     */
    public function testTaskModule($I)
    {
        $I->wantTo('Verify that the tasks module supports creation and members');
        $I->login($I, 'admin', 'admin');
        $I->createUser($I, 'testTasks_testuser', 'password', 'testTasks_test', 'user', 'test@user.com');
        $I->clickCmfiveNavbar($I, 'Task', 'New Task');
        // since there are no task groups, we are redirected and asked to create a task group
        $I->canSeeInCurrentUrl('/task-group/viewtaskgrouptypes#dashboard');
        $I->see('Please set up a taskgroup before continuing');
        $I->createTaskGroup($I, 'testTasks_testgroup', [
            'task_group_type' => 'To Do',
            'can_assign' => 'GUEST',
            'can_view' => 'GUEST',
            'can_create' => 'GUEST',
            'is_active' => 'Yes',
            'description' => 'A test group',
        ]);
        $I->addMemberToTaskGroup($I, 'testTasks_testgroup', 'testTasks_test user', 'MEMBER');
        $I->updateTaskGroup($I, 'testTasks_testgroup', [
            'title' => 'testTasks_testgroup updated',
            'can_assign' => 'MEMBER',
            'can_view' => 'MEMBER',
            'can_create' => 'MEMBER',
            'is_active' => 'Yes',
            'description' => 'A test group updated',
            'default_assignee_id' => 'testTasks_test user'
        ]);

        $I->createTask($I, 'testTasks_testgroup updated', 'testTasks_test task', [
            'task_type' => 'To Do',
            'title' => 'testTasks_test task',
            'status' => 'New',
            'priority' => 'Normal',
            'dt_due' => '2022-11-28',
            'assignee_id' => 'testTasks_test user',
            'estimate_hours' => 10,
            'effort' => 11,
            'description' => 'a test task',
        ]);
        $I->click('Duplicate Task');
        $I->wait(3);
        $I->waitForText('Task duplicated', 6);
        $I->see('testTasks_test task -Copy');
        $I->editTask($I, 'testTasks_test task -Copy', ['status' => 'Wip']);
        $I->wait(3);
        $I->see('Wip');
        $I->updateMemberInTaskGroup($I, 'testTasks_testgroup updated', 'testTasks_test user', 'ALL');
        $I->removeMemberFromTaskGroup($I, 'testTasks_testgroup updated', 'testTasks_test user');
        $I->deleteTaskGroup($I, 'testTasks_testgroup updated');
    }
}
