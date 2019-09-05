<?php
//use \Codeception\Util\Locator;
class AdminModelsLookupCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }
    public function testAdminModelsLookup($I) {
        $I->wantTo('Verify that cmfive admin handles lookups');
        $I->login($I, 'admin','admin');
        $I->createUser($I,'testLookup_testuser' ,'password','testLookup_test','user','test@user.com');
        $I->editUser($I,'testLookup_testuser',['acp_title'=>'Prime Minister']);
        $I->clickCmfiveNavbar($I,'Admin', 'Lookup');
        $I->see('Prime Minister');
        $I->editLookup($I,'Prime Minister', ["cmfive-modal #title"=>'President']);
        $I->clickCmfiveNavbar($I,'Admin', 'List Users');
        $I->dontSee('President');
        $I->clickCmfiveNavbar($I,'Admin', 'Lookup');
        $rowIndex = $I->findTableRowMatching(3,'President');
        $I->click('Delete', 'tbody tr:nth-child('.$rowIndex . ')');
        $I->acceptPopup();
        $I->wait(1);
        $I->see('Cannot delete lookup as it is used as a title for the contacts');
        $I->createLookup($I,'title', 'The Honerable', 'The Honerable');
        $I->see('The Honerable');
        $I->editUser($I,'testLookup_testuser', ['title'=>'The Honerable']);
        $I->clickCmfiveNavbar($I,'Admin', 'Lookup');
        $rowIndex = $I->findTableRowMatching(3,'President');
        $I->click('Delete', 'tbody tr:nth-child('.$rowIndex . ')');
        $I->acceptPopup();
        $I->wait(1);
        $I->see('Lookup Item deleted');
    }
}

