<?php
class TagModuleCest
{
    var $lookupTitle = 'Your Highness';
    var $username = 'TestUser';
    var $firstname = 'Firstname';
    var $lastname = 'Lastname';

    public function testTags($I)
    {

        $I->wantTo('Verify that tags can be created, edited, attached and removed');
        $I->loginAsAdmin($I);
        $I->createUser(
            $I,
            $this->username,
            'password',
            $this->firstname,
            $this->lastname,
            'Firstname@lastname.com',
            [
                // 'comment', 'favorites_user', 'file_upload', 'help_view', 'help_contact', 'inbox_reader', 'inbox_sender',
                'user', 'report_user', 'tag_user'
                //, 'task_admin', 'task_user', 'task_group', 'timelog_user'
            ]
        );
        $I->createTaskGroup($I, 'Test Taskgroup', [
            'task_group_type' => 'To Do',
            'can_assign' => 'MEMBER',
            'can_view' => 'GUEST',
            'can_create' => 'MEMBER',
            'is_active' => 'Yes',
            'description' => 'A test group',
        ]);
        $I->addMemberToTaskGroup($I, 'Test Taskgroup', $this->firstname . ' ' . $this->lastname, 'OWNER');
        $I->createTask($I, 'Test Taskgroup', 'Test Task', [
            'task_type' => 'To Do',
            'title' => 'Test Task',
            'status' => 'New',
            'priority' => 'Normal',
            'dt_due' => strtotime('27/05/1988'),
            'assignee_id' => $this->firstname . ' ' . $this->lastname,
            'estimate_hours' => 10,
            'effort' => 11,
            'description' => 'a test task',
        ]);

        $I->clickCmfiveNavbar($I, 'Task', 'Task Groups');
        $I->click('Test Taskgroup');
        $I->click('Test Task');
        $I->createTag($I, "PRIMACY");
        $I->createTag($I, "Current");
        $I->See("Current");
        $I->See("PRIMACY");
        $I->detachTag($I, "Current");
        $I->dontSee("Current");
        $I->reattachTag($I, "Current");
        $I->See("Current");
        $I->clickCmfiveNavbar($I, 'Tag', 'Tag Admin');
        $I->See("Current");
        $I->See("PRIMACY");
        $I->deleteTag($I, "Current");
        $I->dontSee("Current");
        $I->editTag($I, "PRIMACY", "Starred");
        $I->See("Starred");
        $I->dontSee("Current");
        $I->clickCmfiveNavbar($I, 'Task', 'Task Groups');
        $I->click('Test Taskgroup');
        $I->click('Test Task');
        $I->See("Starred");
    }
}
