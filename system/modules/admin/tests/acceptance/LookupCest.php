<?php

class AdminModelsLookupCest
{
    public function testAdminModelsLookup($I)
    {
        $I->wantTo('Verify that cmfive admin handles lookups');
        $I->login($I, 'admin', 'admin');
        $I->createLookupType($I, 'title', 'Mrs', 'Mrs');
        $I->createUser($I, 'testLookup_testuser', 'password', 'testLookup_test', 'user', 'test@user.com');
        //$I->editUser($I, 'testLookup_testuser', ['title' => 'Prime Minister']);
        $I->editUser($I, 'testLookup_testuser', ['language' => 'Deutsch', 'title_lookup_id' => 'Mrs']);
        $I->clickCmfiveNavbar($I, 'Admin', 'Lookup');
        $I->see('Mrs');
        $I->editLookup($I, 'Mrs', ["cmfive-modal #title" => 'Missus']);
        $I->clickCmfiveNavbar($I, 'Admin', 'List Users');
        $I->dontSee('Mrs');
        $I->clickCmfiveNavbar($I, 'Admin', 'Lookup');
        $rowIndex = $I->findTableRowMatching(3, 'Missus');
        $I->click('Delete', 'tbody tr:nth-child(' . $rowIndex . ')');
        $I->acceptPopup();
        $I->wait(1);
        $I->see('Cannot delete lookup as it is used as a title for the contacts');
        $I->createLookup($I, 'title', 'The Honourable', 'The Honourable');
        $I->see('The Honourable');
        $I->editUser($I, 'testLookup_testuser', ['title' => 'The Honourable']);
        $I->clickCmfiveNavbar($I, 'Admin', 'Lookup');
        $rowIndex = $I->findTableRowMatching(3, 'The Honourable');
        $I->click('Delete', 'tbody tr:nth-child(' . $rowIndex . ')');
        $I->acceptPopup();
        $I->wait(1);
        $I->see('Lookup Item deleted');
    }
}
