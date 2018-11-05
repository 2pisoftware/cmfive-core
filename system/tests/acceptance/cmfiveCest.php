<?php
//use \Codeception\Util\Locator;
class cmfiveCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }
    public function testLookup($I, $scenario) {
        $I->wantTo('Verify that the basic cmfive actions are functioning correctly');
        $I->login('admin','admin');
        $I->createUser('testLookup_testuser' ,'password','testLookup_test','user','test@user.com');
        $I->editUser('testLookup_testuser',['autocomplete:title'=>'Prime Minister']);
        $I->clickCmfiveNavbar('Admin', 'Lookup');
        $I->see('Prime Minister');
        $I->editLookup('Prime Minister', ["//div[@id='cmfive-modal']//input[@id='title']"=>'President',"//div[@id='cmfive-modal']//input[@id='code']"=>'President']);
        $I->clickCmfiveNavbar('Admin', 'List Users');
        $I->see('President');
        $I->clickCmfiveNavbar('Admin', 'Lookup');
        $rowIndex = $I->findTableRowMatching(3,'President');
        $I->click('Delete', 'tbody tr:nth-child('.$rowIndex . ')');
        $I->acceptPopup();
        $I->wait(1);
        $I->see('Cannot delete lookup as it is used as a title for the contacts');
        $I->createLookup('title', 'The Honerable', 'The Honerable');
        $I->see('The Honerable');
        $I->editUser('testLookup_testuser', ['title'=>'The Honerable']);
        $I->clickCmfiveNavbar('Admin', 'Lookup');
        $rowIndex = $I->findTableRowMatching(3,'President');
        $I->click('Delete', 'tbody tr:nth-child('.$rowIndex . ')');
        $I->acceptPopup();
        $I->wait(1);
        $I->see('Lookup Item deleted');
    }
}
