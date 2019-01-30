<?php

class UserCest
{

    private $lookupTitle = "Senior";
    private $username = "TypicalJo";
    private $firstname = "Jo";
    private $lastname = "Typical";

	public function _before()
    {
    }

    public function _after()
    {
    }

	public function testUser($I) {
		$I->wantTo('Verify users, groups & permissions are assignable');
        $I->login($I,'admin','admin');
        $I->createUser($I, $this->username, 'password', $this->firstname, $this->lastname, 'Firstname@lastname.com', ['comment', 'favorites_user', 'file_upload', 'help_view', 'help_contact', 'inbox_reader', 'inbox_sender', 'user', 'report_user', 'tag_user', 'task_user', 'task_group', 'timelog_user']);
        $I->createLookupType($I, 'title', $this->lookupTitle, $this->lookupTitle);
        $I->editUser($I, $this->username, ['title' => $this->lookupTitle]);
        $I->editLookup($I, $this->lookupTitle, ['cmfive-modal #title' => 'The Honourable']);
        $I->clickCmfiveNavbar($I, 'Admin', 'List Users');
        $I->see("The Honourable {$this->firstname} {$this->lastname}");
        $I->createUserGroup($I, 'Test User Group');
        $I->createUserGroup($I, 'Parent Group');
        $I->expectTo('add the Test User Group as a member of the Parent Group');
        $I->addUserGroupMember($I, 'Parent Group', 'TEST USER GROUP');
        $I->see('Test User Group');
        $I->clickCmfiveNavbar($I, 'Admin', 'List Groups');
        $I->see('Parent Group', 'table tr:nth-child(1) td:nth-child(2)');
        //$I->addUserGroupMember($I, 'Test User Group', 'Administrator', true);
        $I->addUserGroupMember($I, 'Test User Group', "The Honourable {$this->firstname} {$this->lastname}");
        $I->editUserGroupPermissions($I, 'Test User Group', ['user', 'report_admin']);
        $I->clickCmfiveNavbar($I, 'Admin', 'List Users');
        $row = $I->findTableRowMatching(1, $this->username);
        $I->click('Permissions', "table tr:nth-child({$row}) td:nth-child(8)");
        $I->seeCheckboxIsChecked('#check_report_admin');
        $disabledStatus = $I->grabAttributeFrom('#check_report_admin', 'disabled');
        $I->assertEquals($disabledStatus, 'true');
        $I->expectTo('See that permissions are freely editable after deleting a user group that locked them');
        $I->deleteUserGroup($I,'Test User Group');
        $I->clickCmfiveNavbar($I, 'Admin', 'List Users');
        $row = $I->findTableRowMatching(1, $this->username);
        $I->click('Permissions', "table tr:nth-child({$row}) td:nth-child(8)");
        $I->dontSeeCheckboxIsChecked('#check_report_admin');
        $disabledStatus = $I->grabAttributeFrom('#check_report_admin', 'disabled');
        $I->assertEquals($disabledStatus, null);
	}
}