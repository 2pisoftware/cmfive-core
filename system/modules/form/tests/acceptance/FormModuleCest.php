<?php

class FormModuleCest
{
    public function testForm($I)
    {
        $I->wantTo('Verify that forms can be created/edited with data entered via applications');
        $I->loginAsAdmin($I);

        $I->createForm($I, 'Test Form Proto', 'For Test Purposes');
        $I->editForm($I, 'Test Form Proto', 'Test Form', 'For Ongoing Purposes');
        $I->clickCmfiveNavbar($I, 'Form', 'Forms');
        $I->wait(1);
        $I->see("Test Form");
        $I->dontSee("Proto");
        $I->createApplication($I, 'Test Application', 'For Test Wrapping');
        $I->clickCmfiveNavbar($I, 'Form', 'Applications');
        $I->wait(1);
        $I->click("Test Application");
        $I->see("Active: Yes");
        $I->attachApplicationForm($I, 'Test Application', 'Test Form');
        $I->wait(2);

        // adding & populating fields:
        // should be delegated to detailed support in helper module!
        $I->addFormField($I, "Test Form", "Name", "nm1", "Text");
        $I->addFormField($I, "Test Form", "Clocked", "clk1", "Time");
        $I->addFormField($I, "Test Form", "Truthed", "trt1", "Yes/No");
        $I->wait(2);
        $I->clickCmfiveNavbar($I, 'Form', 'Applications');
        $I->wait(2);
        $I->click("Test Application");
        $I->click("Add new Test Form");
        $I->wait(2);
        $I->fillField("//input[@id='nm1']", "Wally");
        $I->fillDateTimePicker("clk1", time());
        $I->click("//input[@id='trt1']");
        $I->click("//div[@id='cmfive-modal']//button[1]");
        $I->wait(2);
        $I->see("Description: For Test Wrapping");
        $I->see("Name");
        $I->see("Wally");
        $I->deleteForm($I, "Test Form");
        $I->clickCmfiveNavbar($I, 'Form', 'Applications');
        $I->wait(1);
        $I->click("Test Application");
        $I->see("Description: For Test Wrapping");
        $I->dontSee("Add new Test Form");
    }
}
