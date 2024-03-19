<?php

class AdminModuleCest
{
    public function testAdminModule($I)
    {
        $I->wantTo('Verify that the admin module has basic functions');
        $I->login($I, 'admin', 'admin');
        $I->createUser($I, 'testAdmin_testuser', 'password', 'testAdmin_test', 'user', 'test@user.com');
        $I->clickCmfiveNavbar($I, 'Admin', 'List Users');
        $rowIndex = $I->findTableRowMatching(1, 'testAdmin_testuser');
        $I->click('Delete', '.table-responsive table tbody tr:nth-child(' . $rowIndex . ')');
        $I->click('Delete user');
        $I->acceptPopup();
        $I->clickCmfiveNavbar($I, 'Admin', 'List Users');
        $I->cantSee("testAdmin_testuser");
    }
}
