<?php

namespace Tests\Support\Helper;

class CmfiveAdminModule extends \Codeception\Module
{
    /**
     * Creates a new user.
     *
     * @param CmfiveUI $I
     * @param string $username
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param array[string] $permissions
     *
     * @return void
     */
    public function createUser($I, $username, $password, $firstName, $lastName, $email)
    {
        $I->clickCmfiveNavbar($I, 'Admin', 'List Users');
        $I->click('Add New User');
        $I->waitForElement('#login');
        $I->waitForElement("//button[contains(@class,'savebutton')]");
        $I->wait(1);
        $I->fillForm(
            [
                'login' => $username,
                'password' => $password,
                'password2' => $password,
                'check:is_active' => true,
                'firstname' => $firstName,
                'lastname' => $lastName,
                'email' => $email
            ]
        );
        $I->click('Save');
        $I->waitForElement("//div[@class='tab-head'][a[@href='#internal'] and a[@href='#external']]", 2);
        $I->waitForElement("//div[contains(@class,'alert-box')]", 2);
        $I->see('User ' . $username . ' added');
    }

    public function editUser($I, $user, $data)
    {
        $I->clickCmfiveNavbar($I, 'Admin', 'List Users');
        $rowIndex = $I->findTableRowMatching(1, $user);
        $I->click('Edit', ".table-responsive tbody tr:nth-child({$rowIndex}) td:nth-child(8)");
        //$I->click('Edit', 'tbody tr:nth-child(' . $rowIndex . ')');
        $I->wait(1);

        $fields = [];
        if (!empty($data['first_name'])) {
            $fields['first_name'] = $data[' first_name'];
        }
        if (!empty($data['last_name'])) {
            $fields['last_name'] = $data['last_name'];
        }
        if (!empty($data['other_name'])) {
            $fields['other_name'] = $data['other_name'];
        }
        if (!empty($data['language'])) {
            $fields['select:language'] = $data['language'];
        }
        if (!empty($data['title_lookup_id'])) {
            $fields['select:title_lookup_id'] = $data['title_lookup_id'];
        }
        $I->fillForm($fields);

        //$I->fillForm($data);
        $I->click('Save');
        $I->waitForText("User details updated");
    }

    public function editLookup($I, $lookup, $data)
    {
        $I->wait(1);
        $I->clickCmfiveNavbar($I, 'Admin', 'Lookup');
        $I->wait(1);
        $rowIndex = $I->findTableRowMatching(3, $lookup);
        $I->click('Edit', 'tbody tr:nth-child(' . $rowIndex . ')');
        $I->wait(1);
        $I->fillForm($data);
        $I->wait(1);
        $I->click("//div[@id='cmfive-modal']//button[contains(text(),'Update')]");
        $I->wait(1);
        $I->see('Lookup Item updated');
    }

    public function createLookup($I, $type, $code, $title)
    {
        $I->clickCmfiveNavbar($I, 'Admin', 'Lookup');
        $I->click('New Item');
        $I->click("//div[@id='tab-2']//select[@id='type']");
        $I->click("//div[@id='tab-2']//option[@value='title'][contains(text(),'title')]");
        $I->fillField('#code', $code);
        $I->fillField('#title', $title);
        $I->click(".savebutton");
        $I->wait(1);
        $I->see('Lookup Item created');
    }

    public function createLookupType($I, $type, $code, $title)
    {
        $I->clickCmfiveNavbar($I, 'Admin', 'Lookup');
        $I->click('Create Lookup');
        $I->fillField('#ntype', $type);
        $I->fillField('#code', $code);
        $I->fillField('#title', $title);
        $I->click(".savebutton");
        $I->wait(1);
        $I->see('Lookup Item created');
    }

    public function createUserGroup($I, $name)
    {
        $I->clickCmfiveNavbar($I, 'Admin', 'List Groups');
        $I->click('New Group');
        $I->waitForElement('#title');
        $I->wait(1);
        $I->fillField('#title', $name);
        $I->click('Save');
        $I->waitForText('New group added');
        $I->see($name);
    }

    public function deleteUserGroup($I, $usergroup)
    {
        $I->clickCmfiveNavbar($I, 'Admin', 'List Groups');
        $row = $I->findTableRowMatching(1, $usergroup);
        $I->click('Delete', "table tr:nth-child({$row}) td:nth-child(3)");
        $I->acceptPopup();
        $I->see('Group is deleted');
    }

    public function addUserGroupMember($I, $usergroup, $user, $admin = false)
    {
        $I->clickCmfiveNavbar($I, 'Admin', 'List Groups');
        $row = $I->findTableRowMatching(1, $usergroup);
        $I->click('Edit', "table tr:nth-child({$row}) td:nth-child(3)");
        $I->click('New Member');
        $I->waitForElementClickable('#member_id');
        $I->selectOption('#member_id', $user);
        if ($admin) {
            $I->click('#is_owner');
        }
        $I->click('Save');
    }

    public function editUserGroupPermissions($I, $usergroup, $permissions = [])
    {
        $I->clickCmfiveNavbar($I, 'Admin', 'List Groups');
        $row = $I->findTableRowMatching(1, $usergroup);
        $I->click('Edit', "table tr:nth-child({$row}) td:nth-child(3)");
        $I->click('Edit Permissions');
        if (empty($permissions)) {
            $permissions = ['user'];
        }
        foreach ($permissions as $permission) {
            $I->click('#check_' . $permission);
        }
        $I->scrollTo(['css' => 'button.btn:nth-child(1)'], 220, 250);
        $I->wait(2);
        $I->click('Save');
        $I->see('Permissions are updated');
    }

    public function createTemplate($I, $title, $module, $category, $code)
    {
        $I->clickCmfiveNavbar($I, 'Admin', 'Templates');
        $I->click("Add Template");
        $I->fillField('#title', $title);
        $I->click('#is_active');
        $I->fillField('#module', $module);
        $I->fillField('#category', $category);
        $I->click('Save');
        $I->click('Template');
        $I->waitForElement('#template_title', 2);
        $I->fillField('#template_title', $title);
        if (!$I->isUsingBootstrap5($I)) {
            $I->executeJS("$('.CodeMirror')[0].CodeMirror.setValue(\"" . $code . "\")");
            $I->waitForElement("//div[@id='template']//button[@type='submit']", 2);
            $I->click("//div[@id='template']//button[@type='submit']");
        } else {
            $I->executeJS(
                "const customEvent = new CustomEvent('update', {detail: \"" . $code . "\"});"
                 . "document.querySelector('.code-mirror-target').dispatchEvent(customEvent);"
            );
            $I->waitForElement("//div[@id='tab-2']//button[@type='submit']", 2);
            $I->click("//div[@id='tab-2']//button[@type='submit']");
        }
    }

    public function demoTemplate($I, $title)
    {

        $I->clickCmfiveNavbar($I, 'Admin', 'Templates');
        $I->wait(1);
        $rowIndex = $I->findTableRowMatching(1, $title);
        $I->click('Edit', '.tablesorter tbody tr:nth-child(' . $rowIndex . ') td:nth-child(5)');
        $I->wait(1);
        $I->click("Test Output");
        $I->amOnPage("/admin-templates/rendertemplate/1");
        $I->wait(3);
    }
}
