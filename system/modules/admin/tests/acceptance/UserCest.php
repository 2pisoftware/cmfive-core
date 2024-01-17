<?php

class UserCest
{
    private $lookupTitle = "Senior";
    private $username = "TypicalJo";
    private $firstname = "Jo";
    private $lastname = "Typical";

    public function testUser($I)
    {
        // NB Permissions are now assigned from the Permissions button - not when the User is initially created
        $I->wantTo('Verify users, groups & permissions are assignable');
        $I->login($I, 'admin', 'admin');
        $I->createUser(
            $I,
            $this->username,
            'password',
            $this->firstname,
            $this->lastname,
            'Firstname@lastname.com'
        );
        $I->createLookupType($I, 'title', $this->lookupTitle, $this->lookupTitle);
        $I->editUser($I, $this->username, ['title' => $this->lookupTitle]);
        $I->editLookup($I, $this->lookupTitle, ['cmfive-modal #title' => 'The Honourable']);
        $I->clickCmfiveNavbar($I, 'Admin', 'List Users');
        $I->dontSee("The Honourable {$this->firstname} {$this->lastname}");
        $I->see("{$this->firstname} {$this->lastname}");
        $I->createUserGroup($I, 'Test User Group');
        $I->createUserGroup($I, 'Parent Group');
        $I->expectTo('add the Test User Group as a member of the Parent Group');
        $I->addUserGroupMember($I, 'Parent Group', 'TEST USER GROUP');
        $I->see('Test User Group');
        $I->clickCmfiveNavbar($I, 'Admin', 'List Groups');
        $I->see('Parent Group', 'table tr:nth-child(1) td:nth-child(1)');
        $I->addUserGroupMember($I, 'Test User Group', "{$this->firstname} {$this->lastname}");
        $I->editUserGroupPermissions($I, 'Test User Group', ['user', 'comment']);
        $I->clickCmfiveNavbar($I, 'Admin', 'List Users');
        $row = $I->findTableRowMatching(1, $this->username);
        $I->click('Permissions', ".table-responsive tbody tr:nth-child({$row}) td:nth-child(8)");
        $I->seeCheckboxIsChecked('#check_comment');
        $disabledStatus = $I->grabAttributeFrom('#check_comment', 'disabled');
        $I->assertEquals($disabledStatus, 'true');
        $I->expectTo('See that permissions are freely editable after deleting a user group that locked them');
        $I->deleteUserGroup($I, 'Test User Group');
        $I->clickCmfiveNavbar($I, 'Admin', 'List Users');
        $row = $I->findTableRowMatching(1, $this->username);
        $I->click('Permissions', ".table-responsive tbody tr:nth-child({$row}) td:nth-child(8)");
        $I->dontSeeCheckboxIsChecked('#check_comment');
        $disabledStatus = $I->grabAttributeFrom('#check_comment', 'disabled');
        $I->assertEquals($disabledStatus, null);
    }
}
