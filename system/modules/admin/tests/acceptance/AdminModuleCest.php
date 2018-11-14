<?php
class AdminModuleCest
{

    	 
    public function _before()
    {
    }

    public function _after()
    {
    }

	public function testAdminModule(\Step\Acceptance\CmfiveAdminModule $I) {
         
    $I->wantTo('Verify that the admin module has basic functions');
		$I->login('admin','admin');
    $I->createUser('testAdmin_testuser' ,'password','testAdmin_test','user','test@user.com');
    $I->clickCmfiveNavbar('Admin', 'List Users');
    $rowIndex = $I->findTableRowMatching(1,'testAdmin_testuser');
        $I->click('Remove', 'tbody tr:nth-child('.$rowIndex . ')'); 
        $I->click('Delete user');
        $I->acceptPopup();
        $I->clickCmfiveNavbar('Admin', 'List Users');
       $I->cantSee("testAdmin_testuser");
	}

}
