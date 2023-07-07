<?php

namespace Tests\Support\Helper;

class CmfiveFormModule extends \Codeception\Module
{
    public function createForm($I, $name, $description)
    {
        $I->clickCmfiveNavbar($I, 'Form', 'Forms');
        $I->wait(1);
        $I->click("//button[contains(text(),'Add')]");
        $I->fillField('#title', $name);
        $I->fillField('#description', $description);
        $I->click('Save');
    }

    public function editForm($I, $name, $rename, $description)
    {
        $I->clickCmfiveNavbar($I, 'Form', 'Forms');
        $I->wait(1);
        $rowIndex = $I->findTableRowMatching(1, $name);
        $I->click('Edit', 'tbody tr:nth-child(' . $rowIndex . ')');
        $I->wait(1);
        $I->fillField('#title', $rename);
        $I->fillField('#description', $description);
        $I->click('Save');
    }

    public function deleteForm($I, $name)
    {
        $I->clickCmfiveNavbar($I, 'Form', 'Forms');
        $rowIndex = $I->findTableRowMatching(1, $name);
        $I->click('Delete', 'tbody tr:nth-child(' . $rowIndex . ')');
        $I->wait(1);
        $I->acceptPopup();
        $I->wait(1);
        $I->clickCmfiveNavbar($I, 'Form', 'Forms');
        $I->dontSee($name);
    }

    public function createApplication($I, $name, $description)
    {
        $I->clickCmfiveNavbar($I, 'Form', 'Applications');
        $I->wait(1);
        $I->click("//button[contains(text(),'Create App')]");
        $I->fillField("//label[contains(text(),'Title')]//input", $name);
        $I->fillField("//label[contains(text(),'Description')]//textarea", $description);
        $I->click("//div[@class='switch']");
        $I->wait(1);
        $I->click('Save');
    }

    public function attachApplicationForm($I, $name, $form)
    {
        $I->clickCmfiveNavbar($I, 'Form', 'Applications');
        $I->wait(1);
        $rowIndex = $I->findTableRowMatching(1, $name);
        $I->click('Edit', 'tbody tr:nth-child(' . $rowIndex . ')');
        $I->wait(2);
        $I->click("Attach form");
        $I->wait(1);
        $I->selectOption("//label[contains(text(),'Form')]/select", $form);
        $I->click("//div[@id='form_application_form_modal']//button[1]");
    }

    public function addFormField($I, $form, $name, $key, $type)
    {
        $I->clickCmfiveNavbar($I, 'Form', 'Forms');
        $I->wait(1);
        $I->click($form);
        $I->wait(2);
        $I->click("Add a field");
        $I->wait(1);
        $I->fillField("#name", $name);
        $I->fillField("#technical_name", $key);
        $I->selectOption("//label[contains(text(),'Type')]/select", $type);
        $I->wait(1);
        $I->click("//div[@id='cmfive-modal']//button[1]");
    }
}
